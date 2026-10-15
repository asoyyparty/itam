<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents and folders
     */
    public function index(Request $request)
    {
        $folderId = $request->input('folder_id');
        $currentFolder = null;
        $breadcrumbs = [];

        // Build breadcrumb navigation if inside a folder
        if ($folderId) {
            $currentFolder = DocumentFolder::with('parent')->find($folderId);
            if ($currentFolder) {
                $temp = $currentFolder;
                while ($temp) {
                    array_unshift($breadcrumbs, $temp);
                    $temp = $temp->parent;
                }
            }
        }

        // Subfolders in current view (if searching, show no subfolders or filtered)
        $foldersQuery = DocumentFolder::withCount(['children', 'documents']);
        if ($folderId) {
            $foldersQuery->where('parent_id', $folderId);
        } else {
            $foldersQuery->whereNull('parent_id');
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $foldersQuery->where('name', 'like', "%{$search}%");
        }
        $folders = $foldersQuery->orderBy('name')->get();

        // Pre-compute recursive document counts for displayed folders & current folder
        $allFolderRows = DocumentFolder::all(['id', 'parent_id']);
        $docCountsByFolder = Document::groupBy('folder_id')
            ->selectRaw('folder_id, count(*) as cnt')
            ->pluck('cnt', 'folder_id')
            ->toArray();

        $childrenMap = [];
        foreach ($allFolderRows as $fRow) {
            $childrenMap[$fRow->parent_id ?? 0][] = $fRow->id;
        }

        $calcTotalDocs = function ($fid) use (&$calcTotalDocs, $childrenMap, $docCountsByFolder) {
            $tot = $docCountsByFolder[$fid] ?? 0;
            if (isset($childrenMap[$fid])) {
                foreach ($childrenMap[$fid] as $cid) {
                    $tot += $calcTotalDocs($cid);
                }
            }
            return $tot;
        };

        foreach ($folders as $folder) {
            $folder->total_documents_count = $calcTotalDocs($folder->id);
        }

        if ($currentFolder) {
            $currentFolder->total_documents_count = $calcTotalDocs($currentFolder->id);
        }

        // Documents query
        $documentsQuery = Document::with(['uploadedBy', 'categoryModel', 'folder'])->latest();
        $includeSubfolders = $request->boolean('all_subfolders');

        // If not searching, filter documents by current folder_id
        if (!$request->filled('search')) {
            if ($folderId) {
                if ($includeSubfolders && $currentFolder) {
                    $descendantIds = $currentFolder->getAllDescendantFolderIds();
                    $descendantIds[] = (int) $folderId;
                    $documentsQuery->whereIn('folder_id', $descendantIds);
                } else {
                    $documentsQuery->where('folder_id', $folderId);
                }
            } else {
                $documentsQuery->whereNull('folder_id');
            }
        } else {
            // Global search if keyword is provided
            $search = trim($request->search);
            $documentsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $documentsQuery->where(function($q) use ($request) {
                $q->where('category', $request->category)
                  ->orWhereHas('categoryModel', function($c) use ($request) {
                      $c->where('name', $request->category);
                  });
            });
        }

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $documentsQuery->whereDate('document_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $documentsQuery->whereDate('document_date', '<=', $request->end_date);
        }

        // Statistics
        $totalDocuments = Document::count();
        $totalPdf = Document::where('file_type', 'pdf')->count();
        $totalExcel = Document::whereIn('file_type', ['xls', 'xlsx', 'csv'])->count();
        $totalBytes = Document::sum('file_size');

        $formattedTotalStorage = '0 B';
        if ($totalBytes >= 1073741824) {
            $formattedTotalStorage = number_format($totalBytes / 1073741824, 2) . ' GB';
        } elseif ($totalBytes >= 1048576) {
            $formattedTotalStorage = number_format($totalBytes / 1048576, 2) . ' MB';
        } elseif ($totalBytes >= 1024) {
            $formattedTotalStorage = number_format($totalBytes / 1024, 1) . ' KB';
        } elseif ($totalBytes > 0) {
            $formattedTotalStorage = $totalBytes . ' B';
        }

        $documents = $documentsQuery->paginate(12)->withQueryString();
        $categoriesList = DocumentCategory::orderBy('name')->get();
        $formattedFoldersList = DocumentFolder::getFormattedHierarchyList();
        $allFoldersList = $formattedFoldersList;

        return view('documents.index', compact(
            'documents',
            'folders',
            'currentFolder',
            'breadcrumbs',
            'categoriesList',
            'allFoldersList',
            'formattedFoldersList',
            'totalDocuments',
            'totalPdf',
            'totalExcel',
            'formattedTotalStorage',
            'includeSubfolders'
        ));
    }

    /**
     * Store a newly uploaded document
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'folder_id' => 'nullable|exists:document_folders,id',
            'document_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:255',
            'document_file' => 'required|file|max:20480|mimes:pdf,png,jpg,jpeg,gif,webp,doc,docx,xls,xlsx,zip,rar,7z',
        ]);

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $originalName = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();

            $datePath = Carbon::now()->format('Y/m');
            $storedPath = $file->store("documents/{$datePath}", 'public');

            $documentNumber = Document::generateNextDocumentNumber();
            $categoryModel = DocumentCategory::where('name', $validated['category'])->first();

            Document::create([
                'document_number' => $documentNumber,
                'title' => $validated['title'],
                'category' => $validated['category'],
                'category_id' => $categoryModel ? $categoryModel->id : null,
                'folder_id' => $validated['folder_id'] ?? null,
                'file_path' => $storedPath,
                'file_name' => $originalName,
                'file_type' => $ext,
                'file_size' => $size,
                'document_date' => $validated['document_date'],
                'description' => $validated['description'] ?? null,
                'tags' => $validated['tags'] ?? null,
                'uploaded_by' => Auth::id(),
            ]);

            return redirect()->route('documents.index', ['folder_id' => $validated['folder_id'] ?? null])
                ->with('success', __('messages.document_uploaded_success') ?? 'Dokumen berhasil diunggah!');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    /**
     * Store a new Folder
     */
    public function storeFolder(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:document_folders,id',
            'color' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:500',
        ]);

        DocumentFolder::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'color' => $validated['color'] ?? '#818cf8',
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('documents.index', ['folder_id' => $validated['parent_id'] ?? null])
            ->with('success', 'Folder baru berhasil dibuat!');
    }

    /**
     * Update existing Folder name or color
     */
    public function updateFolder(Request $request, DocumentFolder $folder)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:500',
        ]);

        $folder->update([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? $folder->color,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('documents.index', ['folder_id' => $folder->parent_id])
            ->with('success', 'Folder berhasil diperbarui!');
    }

    /**
     * Remove Folder
     */
    public function destroyFolder(DocumentFolder $folder)
    {
        $parentId = $folder->parent_id;
        $folder->delete();

        return redirect()->route('documents.index', ['folder_id' => $parentId])
            ->with('success', 'Folder berhasil dihapus!');
    }

    /**
     * Store a new Document Category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:document_categories,name',
            'color' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:255',
        ]);

        DocumentCategory::create([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? '#6366f1',
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * Update existing Document Category
     */
    public function updateCategory(Request $request, DocumentCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:document_categories,name,' . $category->id,
            'color' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:255',
        ]);

        $oldName = $category->name;
        $newName = $validated['name'];

        $category->update([
            'name' => $newName,
            'color' => $validated['color'] ?? $category->color,
            'description' => $validated['description'] ?? null,
        ]);

        // Sync string category field in documents table if updated
        if ($oldName !== $newName) {
            Document::where('category_id', $category->id)
                ->orWhere('category', $oldName)
                ->update(['category' => $newName]);
        }

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove Document Category
     */
    public function destroyCategory(DocumentCategory $category)
    {
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus!');
    }

    /**
     * Show document detail JSON for preview / edit modal
     */
    public function show(Document $document)
    {
        return response()->json([
            'id' => $document->id,
            'document_number' => $document->document_number,
            'title' => $document->title,
            'category' => $document->category,
            'category_id' => $document->category_id,
            'folder_id' => $document->folder_id,
            'folder_name' => $document->folder->name ?? 'Root',
            'file_name' => $document->file_name,
            'file_type' => $document->file_type,
            'formatted_size' => $document->formatted_size,
            'document_date' => $document->document_date ? $document->document_date->format('d M Y') : '-',
            'document_date_raw' => $document->document_date ? $document->document_date->format('Y-m-d') : '',
            'description' => $document->description ?? '',
            'tags' => $document->tags ?? '',
            'file_url' => asset('storage/' . implode('/', array_map('rawurlencode', explode('/', $document->file_path)))),
            'is_previewable' => $document->is_previewable,
            'uploaded_by' => $document->uploadedBy->name ?? 'System',
            'file_icon' => $document->file_icon,
        ]);
    }

    /**
     * Update existing document details
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'folder_id' => 'nullable|exists:document_folders,id',
            'document_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:255',
            'document_file' => 'nullable|file|max:20480|mimes:pdf,png,jpg,jpeg,gif,webp,doc,docx,xls,xlsx,zip,rar,7z',
        ]);

        $categoryModel = DocumentCategory::where('name', $validated['category'])->first();

        $updateData = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'category_id' => $categoryModel ? $categoryModel->id : null,
            'folder_id' => !empty($validated['folder_id']) ? (int) $validated['folder_id'] : null,
            'document_date' => $validated['document_date'],
            'description' => $validated['description'] ?? null,
            'tags' => $validated['tags'] ?? null,
        ];

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $originalName = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();

            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $datePath = Carbon::now()->format('Y/m');
            $storedPath = $file->store("documents/{$datePath}", 'public');

            $updateData['file_path'] = $storedPath;
            $updateData['file_name'] = $originalName;
            $updateData['file_type'] = $ext;
            $updateData['file_size'] = $size;
        }

        $document->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui!',
                'document' => $document->fresh(['folder', 'categoryModel'])
            ]);
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diperbarui!');
    }

    /**
     * Move a single document to a target folder
     */
    public function move(Request $request, Document $document)
    {
        $validated = $request->validate([
            'target_folder_id' => 'nullable|exists:document_folders,id',
        ]);

        $targetFolderId = !empty($validated['target_folder_id']) ? (int) $validated['target_folder_id'] : null;
        $document->update(['folder_id' => $targetFolderId]);

        $folderName = $targetFolderId ? (DocumentFolder::find($targetFolderId)->name ?? 'Folder') : 'Root / Arsip Dokumen';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Dokumen '{$document->title}' berhasil dipindahkan ke {$folderName}."
            ]);
        }

        return redirect()->back()->with('success', "Dokumen berhasil dipindahkan ke {$folderName}.");
    }

    /**
     * Copy / duplicate a document
     */
    public function copy(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'target_folder_id' => 'nullable|exists:document_folders,id',
        ]);

        $targetFolderId = array_key_exists('target_folder_id', $validated)
            ? (!empty($validated['target_folder_id']) ? (int) $validated['target_folder_id'] : null)
            : $document->folder_id;

        $newTitle = !empty($validated['title']) ? trim($validated['title']) : ($document->title . ' (Salinan)');

        $newStoredPath = $document->file_path;
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
            $datePath = Carbon::now()->format('Y/m');
            $newFileName = Str::random(40) . ($ext ? '.' . $ext : '');
            $newStoredPath = "documents/{$datePath}/{$newFileName}";
            Storage::disk('public')->copy($document->file_path, $newStoredPath);
        }

        $documentNumber = Document::generateNextDocumentNumber();

        $newDoc = Document::create([
            'document_number' => $documentNumber,
            'title' => $newTitle,
            'category' => $document->category,
            'category_id' => $document->category_id,
            'folder_id' => $targetFolderId,
            'file_path' => $newStoredPath,
            'file_name' => $document->file_name,
            'file_type' => $document->file_type,
            'file_size' => $document->file_size,
            'document_date' => $document->document_date,
            'description' => $document->description,
            'tags' => $document->tags,
            'uploaded_by' => Auth::id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Dokumen '{$newDoc->title}' berhasil disalin!",
                'document' => $newDoc
            ]);
        }

        return redirect()->back()->with('success', "Dokumen berhasil disalin dengan nomor {$newDoc->document_number}!");
    }

    /**
     * Perform bulk operations (move, copy, delete) on multiple documents
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:move,copy,delete',
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id',
            'target_folder_id' => 'nullable|exists:document_folders,id',
        ]);

        $docIds = $validated['document_ids'];
        $targetFolderId = !empty($validated['target_folder_id']) ? (int) $validated['target_folder_id'] : null;
        $targetFolderName = $targetFolderId ? (DocumentFolder::find($targetFolderId)->name ?? 'Folder') : 'Root / Arsip Dokumen';

        if ($validated['action'] === 'move') {
            Document::whereIn('id', $docIds)->update(['folder_id' => $targetFolderId]);
            $msg = count($docIds) . " dokumen berhasil dipindahkan ke {$targetFolderName}.";
        } elseif ($validated['action'] === 'copy') {
            $docs = Document::whereIn('id', $docIds)->get();
            $count = 0;
            foreach ($docs as $doc) {
                $newStoredPath = $doc->file_path;
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                    $datePath = Carbon::now()->format('Y/m');
                    $newFileName = Str::random(40) . ($ext ? '.' . $ext : '');
                    $newStoredPath = "documents/{$datePath}/{$newFileName}";
                    Storage::disk('public')->copy($doc->file_path, $newStoredPath);
                }

                Document::create([
                    'document_number' => Document::generateNextDocumentNumber(),
                    'title' => $doc->title . ' (Salinan)',
                    'category' => $doc->category,
                    'category_id' => $doc->category_id,
                    'folder_id' => $targetFolderId,
                    'file_path' => $newStoredPath,
                    'file_name' => $doc->file_name,
                    'file_type' => $doc->file_type,
                    'file_size' => $doc->file_size,
                    'document_date' => $doc->document_date,
                    'description' => $doc->description,
                    'tags' => $doc->tags,
                    'uploaded_by' => Auth::id(),
                ]);
                $count++;
            }
            $msg = "{$count} dokumen berhasil disalin ke {$targetFolderName}.";
        } elseif ($validated['action'] === 'delete') {
            Document::whereIn('id', $docIds)->delete();
            $msg = count($docIds) . ' dokumen berhasil dihapus.';
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Securely download document
     */
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File dokumen tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Remove document
     */
    public function destroy(Document $document)
    {
        $folderId = $document->folder_id;
        $document->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus']);
        }

        return redirect()->route('documents.index', ['folder_id' => $folderId])
            ->with('success', __('messages.document_deleted_success') ?? 'Dokumen berhasil dihapus.');
    }
}
