<?php

namespace App\Providers;

use App\Models\DirectoryEntry;
use App\Models\DirectoryService;
use App\Observers\DirectoryEntryObserver;
use App\Observers\DirectoryServiceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DirectoryEntry::observe(DirectoryEntryObserver::class);
        DirectoryService::observe(DirectoryServiceObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('bugsnag.multi', \Illuminate\Contracts\Logging\Log::class);
        $this->app->alias('bugsnag.multi', \Psr\Log\LoggerInterface::class);
    }
}
