<?php

namespace App\Http\Controllers;

use App\Http\Requests\Perfil\GuardarHistoriaClinicaRequest;
use App\Models\PatsHistoriaClinica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $pasaporte = null;
        $historiaClinica = null;

        if ($user->id_pasaporte) {
            $pasaporte = DB::table('pats_pasaportes')
                ->where('id_pasaporte', $user->id_pasaporte)
                ->first();

            $historiaClinica = PatsHistoriaClinica::where('id_pasaporte', $user->id_pasaporte)->first();
        }

        return view('perfil.partials.historia-clinica', compact('user', 'pasaporte', 'historiaClinica'));
    }

    public function actualizarCampo(Request $request)
    {
        $campo = $request->input('campo');
        $valor = trim($request->input('valor', ''));

        $permitidos = ['telefono_usuario', 'nombre_usuario', 'nombre_paciente'];

        if (! in_array($campo, $permitidos)) {
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
            'password_nuevo' => ['required', 'min:8', 'confirmed'],
        ], [
            'password_nuevo.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password_nuevo.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = auth('pasaporte')->user();

        if (! Hash::check($request->password_actual, $user->password_hash)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta.'])->withInput();
        }

        DB::table('pats_pasaporte_accesos')
            ->where('id_acceso', $user->id_acceso)
            ->update([
                'password_hash' => Hash::make($request->password_nuevo),
                'debe_cambiar_password' => 0,
                'updated_at' => now(),
            ]);

        return back()->with('password_ok', 'Contraseña actualizada correctamente.');
    }

    public function servirFoto(): mixed
    {
        $user = auth()->user();
        abort_if(! $user->id_pasaporte, 404);

        $path = DB::table('pats_pasaportes')
            ->where('id_pasaporte', $user->id_pasaporte)
            ->value('foto_usuario');

        abort_if(! $path || ! Storage::disk('local')->exists($path), 404, 'Foto no encontrada.');

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg',
            'jpeg' => 'image/jpeg',
            default => 'image/jpeg',
        };

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => $mime,
        ]);
    }

    public function actualizarFoto(Request $request)
    {
        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ]);

        $user = auth()->user();
        if (! $user->id_pasaporte) {
            return response()->json(['error' => 'No tiene un pasaporte asignado para actualizar la foto.'], 403);
        }

        $file = $request->file('foto');
        $ext = preg_replace('/[^a-z0-9]/i', '', strtolower($file->getClientOriginalExtension())) ?: 'jpg';
        $filename = now()->format('YmdHis').'_foto.'.$ext;
        $path = "private/usuarios/{$user->id_acceso}/{$filename}";

        // Borrar foto anterior del disco
        $pasaporte = DB::table('pats_pasaportes')->where('id_pasaporte', $user->id_pasaporte)->first();
        if ($pasaporte && ! empty($pasaporte->foto_usuario)) {
            Storage::disk('local')->delete($pasaporte->foto_usuario);
        }

        Storage::disk('local')->put($path, file_get_contents($file->getRealPath()));

        DB::table('pats_pasaportes')
            ->where('id_pasaporte', $user->id_pasaporte)
            ->update([
                'foto_usuario' => $path,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'url' => route('perfil.foto')]);
    }

    public function guardarHistoriaClinica(GuardarHistoriaClinicaRequest $request)
    {
        $idPasaporte = auth()->user()->id_pasaporte;

        $peso = $request->filled('peso') ? (float) $request->peso : null;
        $altura = $request->filled('altura') ? (float) $request->altura : null;

        $imc = ($peso && $altura && $altura > 0)
            ? round($peso / ($altura ** 2), 2)
            : null;

        PatsHistoriaClinica::updateOrCreate(
            ['id_pasaporte' => $idPasaporte],
            [
                'ocupacion' => $request->ocupacion,
                'estado_civil' => $request->estado_civil,
                'escolaridad' => $request->escolaridad,
                'actividad_fisica' => $request->actividad_fisica,
                'tabaquismo' => $request->tabaquismo,
                'alcohol' => $request->alcohol,
                'alimentacion' => $request->alimentacion,
                'heredo_familiares' => $request->heredo_familiares ?? [],
                'personales_patologicos' => $request->personales_patologicos,
                'personales_no_patologicos' => $request->personales_no_patologicos,
                'enfermedades_previas' => $request->enfermedades_previas,
                'alergias' => $request->alergias,
                'cirugias' => $request->cirugias,
                'medicamentos' => $request->medicamentos,
                'intolerancias' => $request->intolerancias,
                'peso' => $peso,
                'altura' => $altura,
                'imc' => $imc,
            ]
        );

        return back()->with('historia_ok', 'Historia clínica guardada correctamente.');
    }
}
