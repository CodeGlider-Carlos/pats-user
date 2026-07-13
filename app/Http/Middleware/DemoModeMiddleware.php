<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('pasaporte')->user();

        if ($user?->esDemo() && ! in_array($request->method(), ['GET', 'HEAD'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Esta acción no está disponible en modo demostración.',
                ], 403);
            }

            return redirect()->back()->with('demo_blocked', 'Esta acción no está disponible en modo demostración.');
        }

        return $next($request);
    }
}
