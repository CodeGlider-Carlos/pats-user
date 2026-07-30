<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForzarCambioPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('pasaporte')->user();

        if ($user && $user->debe_cambiar_password) {
            // Permitir que salga del loop: la propia ruta de cambio y logout
            if (! $request->routeIs('password.cambiar', 'password.cambiar.post', 'logout')) {
                return redirect()->route('password.cambiar');
            }
        }

        return $next($request);
    }
}
