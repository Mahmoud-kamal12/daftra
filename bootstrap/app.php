<?php

use App\Helpers\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::unauthorized();
            }
            return null;
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::forbidden($e->getMessage() ?: 'Forbidden');
            }
            return null;
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::fail('Validation error', $e->errors());
            }
            return null;
        });
    })
    ->create();
