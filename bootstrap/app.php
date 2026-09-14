<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Credenciais inválidas.',
                ], 401);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('admin/*')) {
                return response()->view('admin.errors.403', [], 403);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('admin/*')) {
                return response()->view('admin.errors.404', [], 404);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Registo não encontrado.',
                ], 404);
            }

            if ($request->is('admin/*')) {
                return response()->view('admin.errors.404', [], 404);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dados inválidos.',
                    'details' => collect($e->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'message' => $messages[0],
                        ];
                    })->values()->all(),
                ], 422);
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e instanceof ModelNotFoundException || str_contains($e->getMessage(), 'No query results for model')) {
                if ($request->is('admin/*')) {
                    return response()->view('admin.errors.404', [], 404);
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'Registo não encontrado.',
                ], 404);
            }

            if ($e instanceof NotFoundHttpException && $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Rota ou recurso não encontrado.',
                ], 404);
            }

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage() ?: 'Erro no servidor.',
                ], $e->getStatusCode());
            }
        });
    })->create();
