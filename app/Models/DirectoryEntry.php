<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectoryEntry extends Model
{
    protected $fillable = [
        'uuid',
        'firstname',
        'lastname',
        'phone',
        'email'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(DirectoryService::class, 'directory_entry_service');
    }
}
