<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Menampilkan semua pengguna
    public function index()
    {
        $users = User::all();

        return response()->json([
            'message' => 'Daftar semua pengguna',
            'data' => $users
        ]);
    }

    // Menambahkan pengguna baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|max:50'
        ]);

        // Hash password sebelum menyimpan
        $validated['password'] = Hash::make($validated['password']);

        $users = User::create($validated);

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => $users
        ], 201);
    }

    // Memperbarui pengguna yang ada
    public function update(Request $request, $id)
    {
        $users = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $users->id,
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $users->id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|string|max:50'
        ]);

        // Hash password jika ada perubahan
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $users->update(array_filter($validated)); // Hanya update field yang ada di request

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui',
            'data' => $users
        ]);
    }

    // Menghapus pengguna
    public function destroy($id)
    {
        $users = User::findOrFail($id);
        $users->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus'
        ]);
    }
}
