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
}
