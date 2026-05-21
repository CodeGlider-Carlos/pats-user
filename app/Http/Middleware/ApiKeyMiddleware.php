<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $configured = config('app.dist_links_api_key', '');

        if (empty($configured) || ! hash_equals($configured, (string) $request->header('X-Api-Key', ''))) {
            return response()->json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
