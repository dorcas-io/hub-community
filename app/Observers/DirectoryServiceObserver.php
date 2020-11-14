<?php

namespace App\Observers;


use App\Models\DirectoryService;
use Ramsey\Uuid\Uuid;

class DirectoryServiceObserver
{
    /**
     * @param DirectoryService $model
     */
    public function creating(DirectoryService $model)
    {
        $model->uuid = Uuid::uuid1()->toString();
    }
}