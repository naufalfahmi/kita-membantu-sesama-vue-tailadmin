<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redirect unauthenticated users to admin signin page
        // For AJAX requests, return 401 JSON response instead of redirect
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                abort(401, 'Unauthenticated');
            }
            return '/admin/signin';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication exceptions for AJAX/API requests
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 401 && ($request->expectsJson() || $request->ajax())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
        });
    })->create();
