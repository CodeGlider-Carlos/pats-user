<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\View\View;

class PortalPasaporteController extends Controller
{
    private const GUARD = 'pasaporte';

    private function acceso(): \App\Models\PatsAcceso
    {
        return auth(self::GUARD)->user();
    }

    // ── Dashboard — Mi Pasaporte ──────────────────────────────────────────────

    public function dashboard(): View
    {
        $acceso = $this->acceso();

        $pasaporte = null;
        $orden     = null;
        $diasVigencia  = 0;
        $estadoColor   = 'danger';
        $estadoTexto   = 'Sin pasaporte';

        if ($acceso->id_pasaporte) {
            $pasaporte = DB::table('pats_pasaportes')
                ->where('id_pasaporte', $acceso->id_pasaporte)
                ->first();
        }

        if ($pasaporte) {
            $vigencia    = Carbon::parse($pasaporte->vigencia);
            $diasVigencia = (int) now()->diffInDays($vigencia, false);

            [$estadoColor, $estadoTexto] = match (true) {
                strtolower($pasaporte->estatus) === 'vencido' => ['danger',  'Vencido'],
                $diasVigencia < 0                             => ['danger',  'Vencido'],
                $diasVigencia <= 15                           => ['warning', 'Por vencer'],
                strtolower($pasaporte->estatus) === 'activo'  => ['success', 'Vigente'],
                default                                       => ['secondary', 'Inactivo'],
            };
        }

        if ($acceso->id_orden) {
            $orden = DB::table('pats_ordenes_pago')
                ->where('id_orden', $acceso->id_orden)
                ->first();
        }

        return view('portal.dashboard', compact(
            'acceso', 'pasaporte', 'orden',
            'diasVigencia', 'estadoColor', 'estadoTexto'
        ));
    }

    // ── Perfil ────────────────────────────────────────────────────────────────

    public function perfil(): View
    {
        $acceso = $this->acceso();

        $pasaporte = null;
        $orden     = null;

        if ($acceso->id_pasaporte) {
            $pasaporte = DB::table('pats_pasaportes')
                ->where('id_pasaporte', $acceso->id_pasaporte)
                ->first();
        }

        if ($acceso->id_orden) {
            $orden = DB::table('pats_ordenes_pago')
                ->where('id_orden', $acceso->id_orden)
                ->first();
        }

        return view('portal.perfil', compact('acceso', 'pasaporte', 'orden'));
    }

    // ── Próximamente (pagos, beneficios, soporte) ─────────────────────────────

    public function proximamente(string $seccion): View
    {
        $labels = [
            'pagos'      => 'Mis Pagos',
            'beneficios' => 'Mis Beneficios',
            'soporte'    => 'Soporte',
        ];

        return view('portal.proximamente', [
            'acceso'  => $this->acceso(),
            'seccion' => $labels[$seccion] ?? ucfirst($seccion),
        ]);
    }
}
