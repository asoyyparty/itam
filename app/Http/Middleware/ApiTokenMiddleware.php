<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming API request using Bearer token authentication.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN') ?? $request->query('api_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token API tidak ditemukan. Silakan login terlebih dahulu.',
            ], 401);
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token API tidak valid atau telah kedaluwarsa.',
            ], 401);
        }

        // Set authenticated user for the request
        Auth::setUser($user);

        return $next($request);
    }
}
