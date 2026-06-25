<?php

use App\Contexts\IdentityAccess\Infrastructure\Http\AgentAuthenticationMiddleware;
use App\SharedKernel\Infrastructure\Http\RequestIdMiddleware;
use App\SharedKernel\Infrastructure\Http\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            RequestIdMiddleware::class,
            SecurityHeadersMiddleware::class,
        ]);

        $middleware->api(prepend: [
            RequestIdMiddleware::class,
        ]);

        $middleware->api(append: [
            SecurityHeadersMiddleware::class,
        ]);

        $middleware->alias([
            'agent.auth' => AgentAuthenticationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $requestId = (string) $request->attributes->get('request_id', '');

            return response()->json([
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => 'Beklenmeyen bir hata oluştu.',
                ],
                'meta' => [
                    'request_id' => $requestId,
                ],
            ], 500);
        });
    })->create();
