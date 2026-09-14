<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Autentikasi berbasis token (Laravel Sanctum "personal access token"),
 * cocok untuk PWA/mobile (bukan cookie/CSRF seperti SPA-mode Sanctum).
 * Menggantikan Firebase Auth di svarga-app — lihat urutan fallback yang
 * sama seperti fitur lain (Laravel API -> Firebase -> mock) di
 * src/services/authService.js.
 *
 * Login tamu (tanpa akun) tetap didukung penuh di FE: endpoint di sini
 * hanya dipakai kalau pengguna memilih login/daftar untuk menyimpan
 * riwayat Mood Tracker lintas perangkat (lihat proposal, bagian 7.3 UX).
 */
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data pendaftaran tidak valid', 'errors' => $validator->errors()], 422);
        }

        $user = UserModel::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken('svarga-app')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Email atau kata sandi tidak valid', 'errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Email atau kata sandi salah.'], 401);
        }

        /** @var UserModel $user */
        $user = UserModel::where('email', $request->input('email'))->firstOrFail();
        $token = $user->createToken('svarga-app')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }
}
