<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectoryService extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'description'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function directoryEntries()
    {
        return $this->belongsToMany(DirectoryEntry::class, 'directory_entry_service');
    }
}
