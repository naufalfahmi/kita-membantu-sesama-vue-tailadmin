<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind permission/role simple aliases to Spatie middleware so resolving
        // the short names like 'permission' works even if alias registration
        // hasn't run yet in some runtime contexts.
        try {
            if (class_exists(\Spatie\Permission\Middlewares\PermissionMiddleware::class)) {
                $this->app->bind('permission', function ($app) {
                    return $app->make(\Spatie\Permission\Middlewares\PermissionMiddleware::class);
                });
            }
            if (class_exists(\Spatie\Permission\Middlewares\RoleMiddleware::class)) {
                $this->app->bind('role', function ($app) {
                    return $app->make(\Spatie\Permission\Middlewares\RoleMiddleware::class);
                });
            }
        } catch (\Throwable $e) {
            // Ignore failures during early bootstrap
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // If session driver is configured to use the database but the
        // `sessions` table is missing, fall back to the file driver to
        // avoid throwing 500s from session middleware during requests.
        try {
            if (config('session.driver') === 'database' && ! Schema::hasTable(config('session.table', 'sessions'))) {
                Log::warning('Session table missing; falling back to file session driver');
                config(['session.driver' => 'file']);
            }
        } catch (\Throwable $e) {
            // Schema may not be ready during some early console commands; ignore.
            Log::debug('Unable to check sessions table existence: ' . $e->getMessage());
        }

        // Register Spatie permission middleware aliases if the package is available
        try {
            // Prefer Spatie middleware when available, otherwise use our fallback
            if (class_exists(\Spatie\Permission\Middlewares\PermissionMiddleware::class)) {
                Route::aliasMiddleware('permission', \Spatie\Permission\Middlewares\PermissionMiddleware::class);
            } else {
                Route::aliasMiddleware('permission', \App\Http\Middleware\PermissionMiddleware::class);
            }

            if (class_exists(\Spatie\Permission\Middlewares\RoleMiddleware::class)) {
                Route::aliasMiddleware('role', \Spatie\Permission\Middlewares\RoleMiddleware::class);
            } else {
                Route::aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
            }
        } catch (\Throwable $e) {
            Log::debug('Unable to register permission middleware aliases: ' . $e->getMessage());
        }
    }
}
