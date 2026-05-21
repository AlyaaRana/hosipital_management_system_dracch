<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email belum diverifikasi. Silakan verifikasi email Anda terlebih dahulu.',
                'data' => null,
                'errors' => [
                    'code' => 403,
                    'detail' => 'Akses dibatasi karena email belum terverifikasi.'
                ]
            ], 403);
        }

        return $next($request);
    }
}
