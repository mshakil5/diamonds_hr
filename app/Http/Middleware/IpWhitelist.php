<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;

class IpWhitelist
{
    public function handle(Request $request, Closure $next)
    {
        $allowed = array_filter(array_map('trim', explode(',', env('ALLOWED_IPS', ''))));

        // if no allowed IPs defined -> allow (change behaviour if you want deny-by-default)
        if (empty($allowed)) {
            return $next($request);
        }

        // Candidates to check (most reliable first)
        $candidates = [];

        // 1) Laravel's computed client IP (respects TrustProxies)
        $candidates[] = $request->ip();

        // 2) ips() array (if multiple)
        foreach ($request->ips() as $ip) {
            $candidates[] = $ip;
        }

        // 3) raw X-Forwarded-For left-most value as fallback
        $xff = $request->header('X-Forwarded-For', '');
        if (!empty($xff)) {
            $leftMost = trim(explode(',', $xff)[0]);
            if ($leftMost) {
                $candidates[] = $leftMost;
            }
        }

        // Normalize and remove duplicates
        $candidates = array_values(array_unique(array_filter($candidates)));

        // Check any candidate against allowed list (supports CIDR)
        foreach ($candidates as $clientIp) {
            if (IpUtils::checkIp($clientIp, $allowed)) {
                return $next($request);
            }
        }

        // Log details for debugging so you can see what Laravel saw
        Log::warning('IP whitelist denied', [
            'allowed'      => $allowed,
            'candidates'   => $candidates,
            'remote_addr'  => $_SERVER['REMOTE_ADDR'] ?? null,
            'xff'          => $xff,
            'url'          => $request->fullUrl(),
            'user_agent'   => $request->userAgent(),
        ]);

        $clientIp = $request->ip();

        if (!IpUtils::checkIp($clientIp, $allowed)) {
            abort(403, "Access denied for IP: {$clientIp}");
        }

    }
}
