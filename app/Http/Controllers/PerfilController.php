<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash};

class PerfilController extends Controller
{
    public function show()
    {
        $user      = auth()->user();
        $pasaporte = null;

        if ($user->id_pasaporte) {
            $pasaporte = DB::table('pats_pasaportes')
                ->where('id_pasaporte', $user->id_pasaporte)
                ->first();
        }

        return view('perfil.partials.historia-clinica', compact('user', 'pasaporte'));
    }

    public function actualizarCampo(Request $request)
    {
        $campo = $request->input('campo');
        $valor = trim($request->input('valor', ''));

        $permitidos = ['telefono_usuario', 'nombre_usuario', 'nombre_paciente'];

        if (!in_array($campo, $permitidos)) {
            return response()->json(['error' => 'Campo no permitido'], 403);
        }

        DB::table('pats_pasaporte_accesos')
            ->where('id_acceso', auth()->user()->id_acceso)
            ->update([$campo => $valor, 'updated_at' => now()]);

        return response()->json(['success' => true, 'valor' => $valor]);
    }

    public function actualizarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => ['required'],
            'password_nuevo'  => ['required', 'min:8', 'confirmed'],
        ], [
            'password_nuevo.min'       => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password_nuevo.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = auth('pasaporte')->user();

        if (!Hash::check($request->password_actual, $user->password_hash)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta.'])->withInput();
        }

        DB::table('pats_pasaporte_accesos')
            ->where('id_acceso', $user->id_acceso)
            ->update([
                'password_hash'          => Hash::make($request->password_nuevo),
                'debe_cambiar_password'  => 0,
                'updated_at'             => now(),
            ]);

        return back()->with('password_ok', 'Contraseña actualizada correctamente.');
    }
}
