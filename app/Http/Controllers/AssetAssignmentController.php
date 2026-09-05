<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssetAssignmentController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('permission:action_manage_assignments', only: ['create', 'store', 'edit', 'update', 'destroy', 'importExcel', 'updateStatus', 'complete', 'generateTag', 'returnAsset', 'pingBatch', 'ping', 'updateAll']),
        ];
    }

    public function index(Request $request)
    {
        $query = AssetAssignment::with(['asset', 'employee', 'employee.department']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('asset_tag', 'like', "%$search%");
            })->orWhereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('assigned_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('assigned_date', '<=', $request->date_to);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $assignments = $query->latest()->paginate(25)->withQueryString();

        $statusCounts = \Illuminate\Support\Facades\Cache::remember('assignment_status_counts', 300, function () {
            return AssetAssignment::selectRaw("status, count(*) as total")
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all();
        });
        $totalAssignmentsCount = array_sum($statusCounts);
        $activeCount = $statusCounts['Assigned'] ?? 0;
        $returnedCount = $statusCounts['Returned'] ?? 0;

        return view('assignments.index', compact('assignments', 'statusCounts', 'totalAssignmentsCount', 'activeCount', 'returnedCount'));
    }

    public function create()
    {
        // Only get assets that are Available
        $assets = \Illuminate\Support\Facades\Cache::remember('available_assets_select', 300, function () {
            return Asset::where('status', 'Available')->select('id', 'name', 'asset_tag')->orderBy('name')->get();
        });
        $employees = \Illuminate\Support\Facades\Cache::remember('active_employees_select', 300, function () {
            return Employee::select('id', 'name', 'employee_id')->orderBy('name')->get();
        });

        return view('assignments.create', compact('assets', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        // Check if asset is still available
        $asset = Asset::findOrFail($request->asset_id);
        if ($asset->status !== 'Available') {
            return back()->with('error', 'Asset is not available for assignment.')->withInput();
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('handovers', 'public');
        }

        $assignment = AssetAssignment::create([
            'asset_id' => $request->asset_id,
            'employee_id' => $request->employee_id,
            'assigned_date' => $request->assigned_date,
            'status' => 'Assigned',
            'notes' => $request->notes,
            'photo' => $photoPath,
        ]);

        // Update asset status
        $asset->update(['status' => 'Assigned']);

        \Illuminate\Support\Facades\Cache::forget('assignment_status_counts');
        \Illuminate\Support\Facades\Cache::forget('asset_status_counts');

        return redirect()->route('assignments.index', request()->query())->with('success', 'Asset successfully assigned.');
    }

    public function edit(AssetAssignment $assignment)
    {
        $employees = Employee::orderBy('name')->get();

        return view('assignments.edit', compact('assignment', 'employees'));
    }

    public function update(Request $request, AssetAssignment $assignment)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $data = [
            'employee_id' => $request->employee_id,
            'assigned_date' => $request->assigned_date,
            'notes' => $request->notes,
        ];

        if ($request->hasFile('photo')) {
            if ($assignment->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($assignment->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($assignment->photo);
            }
            $data['photo'] = $request->file('photo')->store('handovers', 'public');
        }

        $assignment->update($data);

        \Illuminate\Support\Facades\Cache::forget('assignment_status_counts');

        return redirect()->route('assignments.index', request()->query())->with('success', 'Assignment updated successfully.');
    }

    public function destroy(AssetAssignment $assignment)
    {
        // If it's still assigned, make the asset available again before deleting the record.
        if ($assignment->status === 'Assigned') {
            $assignment->asset->update(['status' => 'Available']);
        }
        $assignment->delete();

        \Illuminate\Support\Facades\Cache::forget('assignment_status_counts');
        \Illuminate\Support\Facades\Cache::forget('asset_status_counts');

        return redirect()->route('assignments.index', request()->query())->with('success', 'Assignment record deleted.');
    }

    public function updateStatus(Request $request, AssetAssignment $assignment)
    {
        $request->validate([
            'status' => 'required|in:Assigned,Returned',
        ]);

        if ($request->status === 'Returned' && $assignment->status === 'Assigned') {
            $assignment->update([
                'status' => 'Returned',
                'return_date' => date('Y-m-d'),
            ]);
            $assignment->asset->update(['status' => 'Available']);
        } elseif ($request->status === 'Assigned' && $assignment->status === 'Returned') {
            // Check if asset is still available before returning to Assigned
            if ($assignment->asset->status === 'Available') {
                $assignment->update([
                    'status' => 'Assigned',
                    'return_date' => null,
                ]);
                $assignment->asset->update(['status' => 'Assigned']);
            } else {
                return response()->json(['success' => false, 'message' => 'Asset is no longer available.']);
            }
        }

        \Illuminate\Support\Facades\Cache::forget('assignment_status_counts');
        \Illuminate\Support\Facades\Cache::forget('asset_status_counts');

        return response()->json(['success' => true, 'message' => 'Status updated successfully.', 'status' => $assignment->status]);
    }

    public function returnAsset(Request $request, AssetAssignment $assignment)
    {
        $request->validate([
            'return_date' => ['required', 'date'],
        ]);

        if ($assignment->status !== 'Assigned') {
            return back()->with('error', 'Asset is already returned.');
        }

        $returnDate = \Carbon\Carbon::parse($request->return_date)->format('Y-m-d');

        $assignment->update([
            'return_date' => $returnDate,
            'status' => 'Returned',
            'notes' => $assignment->notes."\nReturned on ".$returnDate.': '.$request->return_notes,
        ]);

        $assignment->asset->update(['status' => 'Available']);

        \Illuminate\Support\Facades\Cache::forget('assignment_status_counts');
        \Illuminate\Support\Facades\Cache::forget('asset_status_counts');

        return redirect()->route('assignments.index', request()->query())->with('success', 'Asset successfully returned.');
    }
}
