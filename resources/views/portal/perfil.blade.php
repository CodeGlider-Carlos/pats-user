@extends('portal.layout')

@section('title', 'Mi Perfil')

@push('styles')
<style>
    .section-title {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--slate-400);
        margin-bottom: 14px;
    }
    .profile-avatar {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: var(--blue-100);
        color: var(--blue-600);
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; font-weight: 800;
        flex-shrink: 0;
    }
    .profile-hero {
        display: flex; align-items: center; gap: 20px;
        margin-bottom: 24px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
    }
    .profile-hero__name  { font-size: 18px; font-weight: 800; color: var(--slate-800); }
    .profile-hero__email { font-size: 13px; color: var(--slate-400); margin-top: 3px; }
    .profile-hero__badges { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1>Mi Perfil</h1>
    <p>Información de tu cuenta y datos de afiliación</p>
</div>

{{-- ── Hero ── --}}
<div class="profile-hero">
    <div class="profile-avatar">
        {{ strtoupper(substr($acceso->nombre_usuario ?? $acceso->nombre_paciente ?? $acceso->correo_usuario, 0, 1)) }}
    </div>
    <div>
        <div class="profile-hero__name">
            @if($pasaporte)
                {{ trim(($pasaporte->apellido_pa ?? '') . ' ' . ($pasaporte->apellido_ma ?? '') . ' ' . ($pasaporte->nombres ?? '')) ?: ($acceso->nombre_paciente ?? $acceso->nombre_usuario ?? '—') }}
            @else
                {{ $acceso->nombre_paciente ?? $acceso->nombre_usuario ?? '—' }}
            @endif
        </div>
        <div class="profile-hero__email">{{ $acceso->correo_usuario }}</div>
        <div class="profile-hero__badges">
            <span class="badge badge-{{ $acceso->estatus === 'ACTIVO' ? 'success' : 'danger' }}">
                <i class="mdi mdi-circle-medium"></i> {{ ucfirst(strtolower($acceso->estatus ?? 'Inactivo')) }}
            </span>
            <span class="badge badge-secondary">
                <i class="mdi mdi-account-outline"></i> {{ ucfirst(strtolower($acceso->tipo_acceso ?? 'Paciente')) }}
            </span>
            @if($pasaporte && $pasaporte->id_pasaporte)
            <span class="badge badge-secondary">
                <i class="mdi mdi-passport"></i> {{ str_pad($pasaporte->id_pasaporte, 8, '0', STR_PAD_LEFT) }}
            </span>
            @endif
        </div>
    </div>
</div>

{{-- ── Datos de acceso ── --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header-bar">
        <i class="mdi mdi-shield-account-outline"></i>
        <h2>Datos de acceso</h2>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item__label">Correo</div>
                <div class="info-item__value">{{ $acceso->correo_usuario }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Teléfono</div>
                <div class="info-item__value">{{ $acceso->telefono_usuario ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Tipo de acceso</div>
                <div class="info-item__value">{{ ucfirst(strtolower($acceso->tipo_acceso ?? 'Paciente')) }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Estatus</div>
                <div class="info-item__value">{{ ucfirst(strtolower($acceso->estatus ?? '—')) }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Último acceso</div>
                <div class="info-item__value">
                    {{ $acceso->ultimo_login ? \Carbon\Carbon::parse($acceso->ultimo_login)->format('d/m/Y H:i') : '—' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Cuenta creada</div>
                <div class="info-item__value">
                    {{ $acceso->created_at ? \Carbon\Carbon::parse($acceso->created_at)->format('d/m/Y') : '—' }}
                </div>
            </div>
        </div>
    </div>
</div>

@if($pasaporte)

{{-- ── Datos personales ── --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header-bar">
        <i class="mdi mdi-account-details-outline"></i>
        <h2>Datos personales</h2>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item__label">Nombre(s)</div>
                <div class="info-item__value">{{ $pasaporte->nombres ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Apellido paterno</div>
                <div class="info-item__value">{{ $pasaporte->apellido_pa ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Apellido materno</div>
                <div class="info-item__value">{{ $pasaporte->apellido_ma ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">CURP</div>
                <div class="info-item__value mono">{{ $pasaporte->curp ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Fecha de nacimiento</div>
                <div class="info-item__value">
                    {{ $pasaporte->fecha_nacimiento ? \Carbon\Carbon::parse($pasaporte->fecha_nacimiento)->format('d/m/Y') : '—' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Sexo</div>
                <div class="info-item__value">{{ $pasaporte->sexo ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Pasaporte ── --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header-bar">
        <i class="mdi mdi-passport"></i>
        <h2>Pasaporte PATS</h2>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item__label">N.º de pasaporte</div>
                <div class="info-item__value mono">{{ str_pad($pasaporte->id_pasaporte, 8, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Estatus</div>
                <div class="info-item__value">{{ strtoupper($pasaporte->estatus ?? '—') }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Vigencia</div>
                <div class="info-item__value">
                    {{ $pasaporte->vigencia ? \Carbon\Carbon::parse($pasaporte->vigencia)->format('d/m/Y') : '—' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Estado / Región</div>
                <div class="info-item__value">{{ $pasaporte->region ?? '—' }}</div>
            </div>
            @if($pasaporte->zona)
            <div class="info-item">
                <div class="info-item__label">Zona</div>
                <div class="info-item__value">{{ $pasaporte->zona }}</div>
            </div>
            @endif
            @if($pasaporte->unidad)
            <div class="info-item">
                <div class="info-item__label">Unidad</div>
                <div class="info-item__value">{{ $pasaporte->unidad }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

@endif

@if($orden)

{{-- ── Domicilio ── --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header-bar">
        <i class="mdi mdi-map-marker-outline"></i>
        <h2>Domicilio</h2>
    </div>
    <div class="card-body">
        <div class="info-grid">
            @if($orden->dom_calle)
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-item__label">Calle y número</div>
                <div class="info-item__value">
                    {{ $orden->dom_calle }}{{ $orden->dom_num_ext ? ' #' . $orden->dom_num_ext : '' }}{{ $orden->dom_num_int ? ' Int. ' . $orden->dom_num_int : '' }}
                </div>
            </div>
            @endif
            @if($orden->dom_colonia)
            <div class="info-item">
                <div class="info-item__label">Colonia</div>
                <div class="info-item__value">{{ $orden->dom_colonia }}</div>
            </div>
            @endif
            @if($orden->dom_municipio)
            <div class="info-item">
                <div class="info-item__label">Municipio / Delegación</div>
                <div class="info-item__value">{{ $orden->dom_municipio }}</div>
            </div>
            @endif
            @if($orden->dom_ciudad)
            <div class="info-item">
                <div class="info-item__label">Ciudad</div>
                <div class="info-item__value">{{ $orden->dom_ciudad }}</div>
            </div>
            @endif
            @if($orden->dom_estado)
            <div class="info-item">
                <div class="info-item__label">Estado</div>
                <div class="info-item__value">{{ $orden->dom_estado }}</div>
            </div>
            @endif
            @if($orden->dom_cp)
            <div class="info-item">
                <div class="info-item__label">Código postal</div>
                <div class="info-item__value mono">{{ $orden->dom_cp }}</div>
            </div>
            @endif
            @if($orden->dom_pais)
            <div class="info-item">
                <div class="info-item__label">País</div>
                <div class="info-item__value">{{ $orden->dom_pais }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

@endif

@endsection
