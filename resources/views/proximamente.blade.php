@extends('layouts.app')

@section('title', $seccion ?? 'Próximamente')

@section('content')
<div style="max-width:600px;margin:80px auto;text-align:center;padding:0 24px;">

    <div style="width:80px;height:80px;background:#eff4ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
        <i class="mdi mdi-rocket-launch-outline" style="font-size:36px;color:#3b74f5;"></i>
    </div>

    <span style="display:inline-block;background:#3b74f5;color:#fff;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-bottom:16px;">
        Próximamente
    </span>

    <h2 style="font-size:26px;font-weight:800;color:#1e293b;margin-bottom:12px;">
        {{ $seccion ?? 'Esta sección' }} está en desarrollo
    </h2>

    <p style="font-size:15px;color:#64748b;line-height:1.7;margin-bottom:32px;">
        Estamos trabajando para traerte esta funcionalidad muy pronto.<br>
        Mientras tanto, puedes acceder a las demás secciones del sistema.
    </p>

    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('perfil') }}"
       style="display:inline-flex;align-items:center;gap:8px;background:#3b74f5;color:#fff;padding:12px 24px;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none;">
        <i class="mdi mdi-arrow-left"></i> Regresar
    </a>

</div>
@endsection
