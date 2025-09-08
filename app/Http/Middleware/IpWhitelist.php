<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class IpWhitelist
{
    public function handle(Request $request, Closure $next)
    {
        $allowed = array_filter(array_map('trim', explode(',', env('ALLOWED_IPS', ''))));

        if (empty($allowed)) {
            return $next($request);
        }

        $clientIp = $request->ip(); 
        if (!IpUtils::checkIp($clientIp, $allowed)) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
