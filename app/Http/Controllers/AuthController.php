<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,manager,teknisi',
        ]);

        $users = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        $token = $users->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Register success',
            'user' => $users,
            'token' => $token,
        ], 201);
        
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Username atau Password salah.'], 401);
        }

        // Hapus token lama jika ada
        $user->tokens()->delete();

        // Buat token baru dengan expired
        $tokenResult = $user->createToken('auth_token');
        $token = $tokenResult->plainTextToken;

        // Simpan waktu kadaluarsa
        $tokenResult->accessToken->expires_at = Carbon::now()->addHours(7); // expired dalam 7 jam
        $tokenResult->accessToken->save();

        return response()->json([
            'message' => 'Login success',
            'user' => $user,
            'token' => $token,
            'expires_at' => $tokenResult->accessToken->expires_at,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->users()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
