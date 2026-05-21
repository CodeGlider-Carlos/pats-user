<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PatsAcceso;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * GET /login
     */
    public function showForm(): View
    {
        return view('auth.login');
    }

    /**
     * POST /login
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $correo = trim($request->input('email'));

        // ── Buscar usuario en pats_pasaporte_accesos ──────
        $user = PatsAcceso::where('correo_usuario', $correo)
            ->where('activo', 1)
            ->first();

        // ── Usuario no existe ─────────────────────────────
        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
        }

        // ── Usuario inactivo ──────────────────────────────
        if (!$user->estaActivo()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Tu cuenta está inactiva. Contacta al administrador.']);
        }

        // ── Usuario bloqueado ─────────────────────────────
        if ($user->estaBloqueado()) {
            $minutos = now()->diffInMinutes($user->bloqueado_hasta);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Cuenta bloqueada por demasiados intentos fallidos. Intenta en {$minutos} min."]);
        }

        // ── Verificar contraseña ──────────────────────────
        if (!Hash::check($request->input('password'), $user->password_hash)) {
            $user->incrementarFallos();

            $intentos = (int) $user->intentos_fallidos + 1;
            $restantes = max(0, 5 - $intentos);

            $msg = $restantes > 0
                ? "Contraseña incorrecta. Te quedan {$restantes} intentos."
                : 'Cuenta bloqueada por 30 minutos por demasiados intentos.';

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $msg]);
        }

        // ── Login exitoso ─────────────────────────────────
        Auth::guard('pasaporte')->login($user, $request->boolean('remember'));
        $user->registrarLogin();

        $request->session()->regenerate();

        return redirect()->intended(route('servicios'));
    }

    /**
     * POST /logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
