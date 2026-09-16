<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*')) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'Sesi login Anda tidak valid atau telah habis. Silakan login kembali.',
                    'data' => null
                ], 401));
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Paksa semua error di route API agar selalu dirender sebagai JSON
        // (meskipun klien lupa mengirimkan header Accept: application/json)
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });

        // Handle 404 (Endpoint & Model Not Found)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $pesan = $e->getPrevious() instanceof ModelNotFoundException
                    ? 'Data yang dicari tidak ditemukan.'
                    : 'Endpoint tidak ditemukan.';

                return response()->json([
                    'success' => false,
                    'message' => $pesan,
                    'data' => null
                ], 404);
            }
        });

        // Handle Validation Error
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors(),
                    'data' => null
                ], $e->status);
            }
        });

        // Handle Authentication Error (jika request memiliki header Accept: application/json)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login Anda tidak valid atau telah habis. Silakan login kembali.',
                    'data' => null
                ], 401);
            }
        });
    })->create();
