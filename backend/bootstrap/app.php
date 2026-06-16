<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\EnsureUserHasRole;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $exception->errors(),
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $exception, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Não autenticado',
                'errors' => [],
            ], 401);
        });

        $exceptions->render(function (HttpExceptionInterface $exception, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = $exception->getStatusCode();

            return response()->json([
                'success' => false,
                'message' => $status === 404 ? 'Recurso não encontrado' : 'Acesso não autorizado',
                'errors' => [],
            ], $status);
        });
    })->create();
