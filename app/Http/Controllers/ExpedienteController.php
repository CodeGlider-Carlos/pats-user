<?php

namespace App\Http\Controllers;

use App\Models\PatsHistoriaClinica;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpedienteController extends Controller
{
    public function show(string $token): mixed
    {
        $pasaporte = DB::table('pats_pasaportes')
            ->where('token_qr', $token)
            ->where('activo', 1)
            ->first();
        abort_if(! $pasaporte, 404, 'Expediente no encontrado.');

        $historiaClinica = PatsHistoriaClinica::where('id_pasaporte', $pasaporte->id_pasaporte)->first();

        $edad = Carbon::parse($pasaporte->fecha_nacimiento)->age;

        $diasVigencia = $pasaporte->fecha_vencimiento_real
            ? (int) now()->diffInDays(Carbon::parse($pasaporte->fecha_vencimiento_real), false)
            : null;

        [$estadoColor, $estadoTexto] = match (true) {
            strtolower($pasaporte->estatus) === 'vencido' => ['danger', 'Vencido'],
            $diasVigencia !== null && $diasVigencia < 0 => ['danger', 'Vencido'],
            $diasVigencia !== null && $diasVigencia <= 7 => ['warning', 'Por vencer'],
            strtolower($pasaporte->estatus) === 'activo' => ['success', 'Vigente'],
            default => ['secondary', 'Inactivo'],
        };

        return view('expediente.show', compact('pasaporte', 'historiaClinica', 'edad', 'estadoColor', 'estadoTexto'));
    }

    public function foto(string $token): mixed
    {
        $path = DB::table('pats_pasaportes')
            ->where('token_qr', $token)
            ->where('activo', 1)
            ->value('foto_usuario');

        abort_if(! $path, 404);

        $fullPath = storage_path('app/'.$path);
        abort_if(! file_exists($fullPath), 404);

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            default => 'image/jpeg',
        };

        return response()->file($fullPath, ['Content-Type' => $mime]);
    }
}
