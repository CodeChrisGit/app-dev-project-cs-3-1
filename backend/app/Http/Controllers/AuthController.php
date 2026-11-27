<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /api/admin/login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! $user->is_admin || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid login details.'], 422);
        }

        return [
            'token' => $user->createToken('admin')->plainTextToken,
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ];
    }

    // POST /api/admin/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    // GET /api/admin/me
    public function me(Request $request)
    {
        return $request->user()->only(['id', 'name', 'email']);
    }
}
