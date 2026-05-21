<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasaporteController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('pasaporte')->user();

        // ── Buscar pasaporte por correo del usuario logueado ──
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        // Si no tiene pasaporte → vista vacía con mensaje
        if (!$pasaporte) {
            return view('pasaporte.index', [
                'pasaporte'   => null,
                'pagos'       => collect(),
                'ultimoPago'  => null,
                'diasVigencia' => 0,
                'estadoColor' => 'danger',
                'estadoTexto' => 'Sin pasaporte',
            ]);
        }

        // ── Historial de pagos ────────────────────────────────
        $pagos = DB::table('pats_pagos_pasaporte')
            ->where('id_pasaporte', $pasaporte->id_pasaporte)
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $ultimoPago = $pagos->first();

        // ── Calcular días de vigencia ─────────────────────────
        $vencimiento  = Carbon::parse($pasaporte->fecha_vencimiento_real);
        $diasVigencia = now()->diffInDays($vencimiento, false); // negativo si vencido

        // ── Color y texto de estatus ──────────────────────────
        [$estadoColor, $estadoTexto] = match (true) {
            $pasaporte->estatus === 'vencido'  => ['danger',  'Vencido'],
            $diasVigencia <= 7                 => ['warning', 'Por vencer'],
            $pasaporte->estatus === 'activo'   => ['success', 'Vigente'],
            default                            => ['secondary', 'Inactivo'],
        };

        // ── Calcular edad ─────────────────────────────────────
        $edad = Carbon::parse($pasaporte->fecha_nacimiento)->age;

        return view('pasaporte.index', [
            'pasaporte'    => $pasaporte,
            'pagos'        => $pagos,
            'ultimoPago'   => $ultimoPago,
            'diasVigencia' => $diasVigencia,
            'estadoColor'  => $estadoColor,
            'estadoTexto'  => $estadoTexto,
            'edad'         => $edad,
            'vencimiento'  => $vencimiento,
        ]);
    }
}
