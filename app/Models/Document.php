<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_number',
        'folder_id',
        'title',
        'category',
        'category_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'document_date',
        'description',
        'tags',
        'relatable_type',
        'relatable_id',
        'uploaded_by',
    ];

    protected $casts = [
        'document_date' => 'date',
    ];

    /**
     * Relationship with DocumentFolder
     */
    public function folder()
    {
        return $this->belongsTo(DocumentFolder::class, 'folder_id');
    }

    /**
     * Relationship with DocumentCategory
     */
    public function categoryModel()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    /**
     * Relationship with User (uploader)
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Polymorphic relation to any model (Asset, Employee, BeritaAcara, Vendor)
     */
    public function relatable()
    {
        return $this->morphTo();
    }

    /**
     * Format file size to human-readable string (KB, MB, GB)
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        } elseif ($bytes > 0) {
            return $bytes . ' B';
        }
        return '0 B';
    }

    /**
     * FontAwesome Icon and color based on file type
     */
    public function getFileIconAttribute(): array
    {
        $ext = strtolower($this->file_type);
        switch ($ext) {
            case 'pdf':
                return ['icon' => 'fas fa-file-pdf', 'color' => '#ef4444', 'bg' => 'rgba(239, 68, 68, 0.12)'];
            case 'png':
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'webp':
            case 'svg':
                return ['icon' => 'fas fa-file-image', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.12)'];
            case 'doc':
            case 'docx':
                return ['icon' => 'fas fa-file-word', 'color' => '#3b82f6', 'bg' => 'rgba(59, 130, 246, 0.12)'];
            case 'xls':
            case 'xlsx':
            case 'csv':
                return ['icon' => 'fas fa-file-excel', 'color' => '#22c55e', 'bg' => 'rgba(34, 197, 94, 0.12)'];
            case 'zip':
            case 'rar':
            case '7z':
                return ['icon' => 'fas fa-file-archive', 'color' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.12)'];
            default:
                return ['icon' => 'fas fa-file-alt', 'color' => '#6366f1', 'bg' => 'rgba(99, 102, 241, 0.12)'];
        }
    }

    /**
     * Check if file can be previewed directly in browser (PDF or Image)
     */
    public function getIsPreviewableAttribute(): bool
    {
        $ext = strtolower($this->file_type);
        return in_array($ext, ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'xls', 'xlsx', 'csv', 'doc', 'docx', 'zip']);
    }

    /**
     * Generate automatic Document Number in format DOC/YYYY/MM/XXX
     */
    public static function generateNextDocumentNumber(): string
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $prefix = "DOC/{$year}/{$month}/";

        $lastDoc = self::where('document_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastDoc) {
            $lastNum = (int) substr($lastDoc->document_number, strrpos($lastDoc->document_number, '/') + 1);
            $nextNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '001';
        }

        return $prefix . $nextNum;
    }
}
