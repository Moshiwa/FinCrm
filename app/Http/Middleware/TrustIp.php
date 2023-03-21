<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrustIp
{
    private array $whitelist = [
        '195.211.122.249'
    ];

    public function handle(Request $request, Closure $next)
    {
        $ip = $this->getIp();

        $access = false;
        foreach ($this->whitelist as $whitelist_ip) {
            if ($whitelist_ip === $ip) {
                $access = true;
            }
        }

        if (empty($access)) {
            abort(403, 'Your IP address is not in service');
        }

        return $next($request);
    }

    private function getIp() {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $parts = explode(',', $_SERVER[$key]);
                $ip = trim(end($parts));
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }
}
