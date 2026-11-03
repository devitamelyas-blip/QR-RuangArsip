<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class RestrictIpAddress
{
    /**
     * Batasi akses ke alamat IP atau rentang CIDR yang diizinkan di config('arsip.allowed_ip_ranges').
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRanges = config('arsip.allowed_ip_ranges', []);

        // Jika tidak ada konfigurasi ALLOWED_IP_RANGES di .env, lewati pembatasan (non-aktif)
        if (empty($allowedRanges)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        if ($clientIp && IpUtils::checkIp($clientIp, $allowedRanges)) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN, 'Akses ditolak: Aplikasi ini hanya dapat diakses melalui jaringan WiFi internal kantor.');
    }
}

