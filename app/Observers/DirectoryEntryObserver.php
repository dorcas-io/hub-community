<?php

namespace App\Observers;


use App\Models\DirectoryEntry;
use Ramsey\Uuid\Uuid;

class DirectoryEntryObserver
{
    /**
     * @param DirectoryEntry $model
     */
    public function creating(DirectoryEntry $model)
    {
        $model->uuid = Uuid::uuid1()->toString();
    }
}