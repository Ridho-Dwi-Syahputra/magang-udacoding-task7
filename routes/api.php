<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// Endpoint yang butuh token. Sekarang semua task juga dilindungi.
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', fn (Illuminate\Http\Request $request) => response()->json([
        'success' => true,
        'message' => 'Data profil berhasil diambil.',
        'data' => $request->user(),
    ]));
    
    // CRUD Tasks
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::patch('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
