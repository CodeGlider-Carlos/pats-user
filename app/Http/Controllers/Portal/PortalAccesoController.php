<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\View\View;

class PortalAccesoController extends Controller
{
    private const GUARD = 'pasaporte';

    public function showLogin(): View
    {
        return view('portal.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $correo     = strtolower(trim($request->input('correo', '')));
        $password   = $request->input('password', '');

        if ($correo === '' || $password === '') {
            return back()->withErrors(['correo' => 'Ingresa tu correo y contraseña.'])->withInput();
        }

        $acceso = \App\Models\PatsAcceso::where('correo_usuario', $correo)
            ->where('activo', 1)
            ->first();

        if (!$acceso) {
            return back()->withErrors(['correo' => 'Correo o contraseña incorrectos.'])->withInput();
        }

        if ($acceso->estaBloqueado()) {
            $minutos = now()->diffInMinutes($acceso->bloqueado_hasta);
            return back()->withErrors(['correo' => "Cuenta bloqueada. Intenta en {$minutos} min."])->withInput();
        }

        if (!Hash::check($password, $acceso->password_hash)) {
            $acceso->incrementarFallos();
            $restantes = max(0, 5 - ($acceso->intentos_fallidos + 1));
            $msg = $restantes > 0
                ? "Correo o contraseña incorrectos. Te quedan {$restantes} intentos."
                : 'Cuenta bloqueada por 30 minutos por múltiples intentos fallidos.';
            return back()->withErrors(['correo' => $msg])->withInput();
        }

        if (!$acceso->estaActivo()) {
            return back()->withErrors(['correo' => 'Tu cuenta está inactiva. Contacta a soporte.'])->withInput();
        }

        Auth::guard(self::GUARD)->login($acceso, (bool) $request->input('remember'));
        $acceso->registrarLogin();

        $request->session()->regenerate();

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard(self::GUARD)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
    }
}
