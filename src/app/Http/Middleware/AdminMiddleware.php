<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Menggunakan Auth facade bawaan agar deteksi tipe data di VS Code akurat dan menghilangkan garis merah
        if (!Auth::check() || Auth::user()->role !== 'admin') {

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }

            abort(
                403,
                'Akses ditolak. Hanya Admin yang dapat mengakses halaman ini.'
            );
        }

        return $next($request);
    }
}
