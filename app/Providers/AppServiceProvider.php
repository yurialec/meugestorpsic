<?php

namespace App\Providers;

use App\Contracts\Audit\AuditLogger;
use App\Services\Audit\DatabaseAuditLogger;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuditLogger::class, DatabaseAuditLogger::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
