<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Dipakai bersama 'auth:sanctum' untuk endpoint yang cuma boleh diakses
// dashboard admin (svarga-admin) — mis. daftar pengguna, notifikasi
// sistem, buat zona geofencing. Endpoint baca yang aman dilihat publik
// (sensor, koridor, festival, dst.) TIDAK memakai middleware ini.
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user('sanctum')?->is_admin) {
            return response()->json(['message' => 'Endpoint ini khusus admin.'], 403);
        }

        return $next($request);
    }
}
