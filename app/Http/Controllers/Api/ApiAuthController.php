<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    /**
     * Handle mobile login request (supports login via email or username).
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->input('login'));
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($fieldType, $loginInput)->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial login tidak cocok. Periksa email/username dan kata sandi Anda.',
            ], 401);
        }

        // Generate unhashed raw token to return to mobile app, store plain/hashed token
        $rawToken = Str::random(60);
        $user->api_token = $rawToken;
        $user->save();

        // Get user roles and permissions
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $rawToken,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'roles' => $roles,
                    'permissions' => $permissions,
                ],
            ],
        ]);
    }

    /**
     * Handle mobile logout request (revokes API token).
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Get authenticated mobile user profile.
     */
    public function me(Request $request)
    {
        $user = Auth::user();
        $user->loadMissing(['roles']);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ],
            ],
        ]);
    }
}
