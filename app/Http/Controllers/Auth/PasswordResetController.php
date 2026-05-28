<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\PatsAcceso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showSolicitud(): View
    {
        return view('auth.password.olvide');
    }

    public function enviar(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email'    => 'Ingresa un correo válido.',
        ]);

        $correo = trim($request->input('email'));

        $user = PatsAcceso::where('correo_usuario', $correo)
            ->where('activo', 1)
            ->first();

        // Siempre respondemos igual para evitar enumeración de usuarios
        if (!$user) {
            return back()->with('status', 'Si el correo está registrado recibirás un enlace en breve.');
        }

        $token = Str::random(64);

        $user->update([
            'token_reset'        => Hash::make($token),
            'token_reset_expira' => now()->addMinutes(60),
        ]);

        Mail::to($user->correo_usuario)->send(
            new ResetPasswordMail(
                nombre: $user->nombre_usuario ?? $user->nombre_paciente ?? 'Usuario',
                token: $token,
                correo: $user->correo_usuario,
            )
        );

        return back()->with('status', 'Si el correo está registrado recibirás un enlace en breve.');
    }

    public function showReset(Request $request, string $token): View
    {
        return view('auth.password.reset', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetear(Request $request): RedirectResponse
    {
        $request->validate([
            'token'     => ['required'],
            'email'     => ['required', 'email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required'      => 'El correo es obligatorio.',
            'email.email'         => 'Ingresa un correo válido.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'  => 'Las contraseñas no coinciden.',
        ]);

        $correo = trim($request->input('email'));

        $user = PatsAcceso::where('correo_usuario', $correo)
            ->whereNotNull('token_reset')
            ->where('token_reset_expira', '>', now())
            ->first();

        if (!$user || !Hash::check($request->input('token'), $user->token_reset)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'El enlace es inválido o ha expirado. Solicita uno nuevo.']);
        }

        $user->update([
            'password_hash'         => Hash::make($request->input('password')),
            'token_reset'           => null,
            'token_reset_expira'    => null,
            'debe_cambiar_password' => false,
            'password_temporal'     => false,
        ]);

        return redirect()->route('login')
            ->with('status', 'Contraseña actualizada. Ya puedes iniciar sesión.');
    }
}
