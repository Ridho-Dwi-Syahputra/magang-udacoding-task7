<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $kredensial = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $kredensial['email'])->first();

        // Pesannya sengaja disamain buat email salah dan password salah,
        // biar orang nggak bisa nebak-nebak email mana yang kedaftar.
        if (! $user || ! Hash::check($kredensial['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ])->status(401);
        }

        // Hapus semua token lama milik user ini (Single Session)
        // Jadi kalau dia login ulang, token yang lama otomatis hangus.
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $user->createToken('api_token')->plainTextToken,
            ]
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil, token sudah dicabut.',
            'data' => null,
        ]);
    }
}
