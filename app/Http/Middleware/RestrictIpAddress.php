<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class RestrictIpAddress
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRanges = config('arsip.allowed_ip_ranges', []);

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
