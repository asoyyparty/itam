<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentFolder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'parent_id',
        'color',
        'description',
        'created_by',
    ];

    /**
     * Parent folder relationship
     */
    public function parent()
    {
        return $this->belongsTo(DocumentFolder::class, 'parent_id');
    }

    /**
     * Sub-folders relationship
     */
    public function children()
    {
        return $this->hasMany(DocumentFolder::class, 'parent_id');
    }

    /**
     * Documents inside this folder
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'folder_id');
    }

    /**
     * Creator of the folder
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Total items inside (subfolders + documents)
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->children()->count() + $this->documents()->count();
    }

    /**
     * Get all descendant folder IDs recursively
     */
    public function getAllDescendantFolderIds(): array
    {
        $ids = [];
        $children = $this->children()->get(['id']);
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllDescendantFolderIds());
        }
        return $ids;
    }

    /**
     * Total documents count including all subfolders
     */
    public function getTotalDocumentsCountAttribute(): int
    {
        $folderIds = $this->getAllDescendantFolderIds();
        $folderIds[] = $this->id;
        return Document::whereIn('folder_id', $folderIds)->count();
    }

    /**
     * Get all folders formatted with hierarchical path breadcrumbs
     */
    public static function getFormattedHierarchyList()
    {
        $all = self::all();
        $map = $all->keyBy('id');

        $buildPath = function ($folder) use (&$buildPath, $map) {
            $path = [$folder->name];
            $curr = $folder;
            while ($curr && $curr->parent_id && isset($map[$curr->parent_id])) {
                $curr = $map[$curr->parent_id];
                array_unshift($path, $curr->name);
            }
            return implode(' / ', $path);
        };

        return $all->map(function ($folder) use ($buildPath) {
            $folder->hierarchy_path = $buildPath($folder);
            return $folder;
        })->sortBy('hierarchy_path', SORT_NATURAL | SORT_FLAG_CASE)->values();
    }
}
