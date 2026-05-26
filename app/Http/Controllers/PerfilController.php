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

    public function actualizarFoto(Request $request)
    {
        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ]);

        $user = auth()->user();
        if (!$user->id_pasaporte) {
            return response()->json(['error' => 'No tiene un pasaporte asignado para actualizar la foto.'], 403);
        }

        $file = $request->file('foto');
        $filename = 'user_' . $user->id_acceso . '_' . time() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('images/users/' . $user->id_acceso);

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Obtener pasaporte actual para borrar foto vieja
        $pasaporte = DB::table('pats_pasaportes')->where('id_pasaporte', $user->id_pasaporte)->first();
        if ($pasaporte && $pasaporte->foto_usuario) {
            $oldPath = public_path($pasaporte->foto_usuario);
            if (file_exists($oldPath) && is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $file->move($directory, $filename);

        $fotoPath = 'images/users/' . $user->id_acceso . '/' . $filename;

        DB::table('pats_pasaportes')
            ->where('id_pasaporte', $user->id_pasaporte)
            ->update([
                'foto_usuario' => $fotoPath,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'url' => asset($fotoPath)]);
    }
}
