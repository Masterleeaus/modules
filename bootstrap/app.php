<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/settings.php'));
            Route::middleware('web')
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->validateCsrfTokens(except: ['stripe/webhook', 'health', 'health/ready']);

        $middleware->alias([
            'role'                  => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'            => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'subscription'          => \App\Http\Middleware\CheckSubscription::class,
            'technician.limit'      => \App\Http\Middleware\CheckTechnicianLimit::class,
            'setup.complete'        => \App\Http\Middleware\RequireSetupComplete::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        \Sentry\Laravel\Integration::handles($exceptions);

        $exceptions->render(function (
            \Spatie\Permission\Exceptions\UnauthorizedException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->header('X-Inertia')) {
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have access to that area.');
            }
            if ($request->is('api/*')) {
                return ApiResponse::error('FORBIDDEN', 'You do not have permission to perform this action.', 403);
            }
            return response()->json(['message' => 'Forbidden.'], 403);
        });

        // ── API envelope error responses (all routes under /api/*) ──────────────

        $exceptions->render(function (AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('UNAUTHENTICATED', 'Authentication required.', 401);
            }
        });

        $exceptions->render(function (ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::validationError($e->errors());
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('NOT_FOUND', 'The requested resource could not be found.', 404);
            }
        });

        $exceptions->render(function (HttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                $status = $e->getStatusCode();
                [$code, $message] = match ($status) {
                    401 => ['UNAUTHENTICATED', 'Authentication required.'],
                    403 => ['FORBIDDEN', 'You do not have permission to perform this action.'],
                    404 => ['NOT_FOUND', 'The requested resource could not be found.'],
                    405 => ['METHOD_NOT_ALLOWED', 'HTTP method not allowed.'],
                    default => ['HTTP_ERROR', $e->getMessage() ?: 'An error occurred.'],
                };

                return ApiResponse::error($code, $message, $status);
            }
        });
    })->create();
