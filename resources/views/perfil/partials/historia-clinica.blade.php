@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
    <style>
        /* ===========================
                   ESTILOS UNIFICADOS CON PASAPORTE
                   =========================== */

        :root {
            --border: #e2e8f0;
            --navy: #f8fafc;
            --blue: #2563eb;
            --cream: #1e3a5f;
            --text: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --danger: #dc2626;
            --success: #10b981;
            --warning: #f59e0b;
            --transition: 0.2s ease;
            --radius-md: 8px;
            --radius-lg: 16px;
            --radius-sm: 6px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .digi-container-perfil {
            display: block;
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Page Header */
        .digi-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .digi-page-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--cream);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .digi-page-title__icon {
            font-size: 2rem;
            color: var(--blue);
        }

        .digi-page-subtitle {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text-muted);
            margin: 0.25rem 0 0 0;
        }

        /* Passport Card */
        .digi-passport-card {
            background: linear-gradient(135deg, var(--white), var(--navy));
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .digi-passport-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, var(--blue), var(--cream));
            opacity: 0.03;
            border-radius: 50%;
            transform: translate(100px, -100px);
        }

        .digi-passport-photo {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--white);
            box-shadow: var(--shadow-lg);
            margin-bottom: 1rem;
            transition: transform var(--transition);
        }

        .digi-passport-photo:hover {
            transform: scale(1.02);
        }

        .digi-passport-id {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--text-muted);
            background: var(--white);
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            display: inline-block;
            border: 1px solid var(--border);
        }

        .digi-passport-id i {
            color: var(--blue);
            margin-right: 0.25rem;
        }

        .digi-passport-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            /* background: linear-gradient(135deg, var(--blue), var(--cream)); */
            color: white;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            gap: 0.5rem;
        }

        .digi-passport-badge i {
            font-size: 1rem;
        }

        .digi-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .digi-info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        .digi-info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .digi-info-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--text-muted);
        }

        .digi-info-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text);
        }

        .digi-info-value--highlight {
            color: var(--blue);
            font-weight: 600;
        }

        /* Card */
        .digi-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .digi-card__header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(to right, var(--white), var(--navy));
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .digi-card__title {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--cream);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .digi-card__title i {
            color: var(--blue);
            font-size: 1.4rem;
        }

        .digi-card__body {
            padding: 1.5rem;
        }

        /* Editable Field */
        .digi-editable-field {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 0.75rem;
            transition: all var(--transition);
        }

        .digi-editable-field:hover {
            background: var(--navy);
        }

        .digi-view-mode {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
        }

        .digi-view-mode__value {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text);
        }

        .digi-edit-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition);
        }

        .digi-edit-btn:hover {
            background: var(--white);
            color: var(--blue);
            box-shadow: var(--shadow-sm);
        }

        .digi-edit-mode {
            margin-top: 0.5rem;
        }

        .digi-edit-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
            margin-top: 0.5rem;
        }

        /* Form Controls */
        .digi-form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            transition: all var(--transition);
            background: var(--white);
        }

        .digi-form-control:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .digi-form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--white);
            cursor: pointer;
        }

        .digi-form-select:focus {
            outline: none;
            border-color: var(--blue);
        }

        .digi-form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Password Field */
        .digi-password-field {
            position: relative;
        }

        .digi-password-input {
            padding-right: 3rem;
        }

        .digi-toggle-password {
            position: absolute;
            top: 50%;
            right: 0.5rem;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition);
        }

        .digi-toggle-password:hover {
            background: var(--navy);
            color: var(--blue);
        }

        /* Password Strength */
        .digi-strength-meter {
            margin-top: 0.5rem;
        }

        .digi-strength-bar {
            height: 6px;
            background: var(--border);
            border-radius: 9999px;
            overflow: hidden;
            margin-bottom: 0.25rem;
        }

        .digi-strength-progress {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background-color 0.3s;
        }

        .digi-strength-progress.weak {
            background: var(--danger);
            width: 25%;
        }

        .digi-strength-progress.regular {
            background: var(--warning);
            width: 50%;
        }

        .digi-strength-progress.good {
            background: var(--blue);
            width: 75%;
        }

        .digi-strength-progress.strong {
            background: var(--success);
            width: 100%;
        }

        .digi-strength-text {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Section Block */
        .digi-section-block {
            background: linear-gradient(135deg, var(--white), var(--navy));
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .digi-section-block:last-child {
            margin-bottom: 0;
        }

        .digi-section-title {
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--cream);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .digi-section-title i {
            color: var(--blue);
            font-size: 1.2rem;
        }

        .digi-section-title--danger {
            color: var(--danger);
        }

        .digi-section-title--danger i {
            color: var(--danger);
        }

        /* Checkbox Cards */
        .digi-check-card {
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1rem;
            transition: all var(--transition);
            cursor: pointer;
            height: 100%;
        }

        .digi-check-card:hover {
            border-color: var(--blue);
            background: var(--navy);
        }

        .digi-check-card input[type="checkbox"] {
            margin-right: 0.5rem;
            accent-color: var(--blue);
        }

        .digi-check-card label {
            font-size: 0.95rem;
            color: var(--text);
            cursor: pointer;
            width: 100%;
        }

        /* Radio Options */
        .digi-radio-option {
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0.75rem 1.5rem;
            transition: all var(--transition);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .digi-radio-option:hover {
            border-color: var(--blue);
            background: var(--navy);
        }

        .digi-radio-option input[type="radio"] {
            accent-color: var(--blue);
            margin: 0;
        }

        .digi-radio-option span {
            font-size: 0.95rem;
            color: var(--text);
        }

        /* Buttons */
        .digi-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
        }

        .digi-btn--primary {
            background: var(--blue);
            color: white;
        }

        .digi-btn--primary:hover {
            background: var(--cream);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .digi-btn--outline {
            background: transparent;
            border-color: var(--border);
            color: var(--text);
        }

        .digi-btn--outline:hover {
            border-color: var(--blue);
            color: var(--blue);
            background: var(--navy);
        }

        .digi-btn--sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        /* Divider */
        .digi-divider {
            border: 0;
            border-top: 2px solid var(--border);
            margin: 2rem 0;
            opacity: 0.5;
        }

        /* Utilities */
        .d-none {
            display: none;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .text-end {
            text-align: right;
        }
    </style>

    @php
        $nombreCompleto = $pasaporte
            ? trim(($pasaporte->nombres ?? '') . ' ' . ($pasaporte->apellido_pa ?? '') . ' ' . ($pasaporte->apellido_ma ?? ''))
            : ($user->nombre_paciente ?? $user->nombre_usuario ?? '—');

        $campos = [
            'nombre_usuario'   => ['label' => 'Nombre de usuario',   'valor' => $user->nombre_usuario    ?? '', 'editable' => true],
            'telefono_usuario' => ['label' => 'Teléfono',            'valor' => $user->telefono_usuario  ?? '', 'editable' => true],
            'correo_usuario'   => ['label' => 'Correo',              'valor' => $user->correo_usuario    ?? '', 'editable' => false],
            'nombres'          => ['label' => 'Nombres',             'valor' => $pasaporte->nombres      ?? '—', 'editable' => false],
            'apellido_pa'      => ['label' => 'Apellido paterno',    'valor' => $pasaporte->apellido_pa  ?? '—', 'editable' => false],
            'apellido_ma'      => ['label' => 'Apellido materno',    'valor' => $pasaporte->apellido_ma  ?? '—', 'editable' => false],
            'curp'             => ['label' => 'CURP',                'valor' => $pasaporte->curp         ?? '—', 'editable' => false],
            'fecha_nacimiento' => ['label' => 'Fecha de nacimiento', 'valor' => $pasaporte->fecha_nacimiento ? \Carbon\Carbon::parse($pasaporte->fecha_nacimiento)->format('d/m/Y') : '—', 'editable' => false],
            'estatus'          => ['label' => 'Estatus pasaporte',   'valor' => strtoupper($pasaporte->estatus ?? '—'), 'editable' => false],
        ];

        $antecedentes = ['Diabetes', 'Hipertensión', 'Cáncer', 'Ninguno'];
    @endphp

    <div class="digi-container-perfil py-4">
        {{-- Header --}}
        <div class="digi-page-header">
            <div>
                <h1 class="digi-page-title">
                    <i class="mdi mdi-account-circle digi-page-title__icon"></i>
                    Mi perfil
                </h1>
                <p class="digi-page-subtitle">
                    Información personal y clínica del paciente
                </p>
            </div>
        </div>

        {{-- Bloque 1: Identidad / Pasaporte --}}
        <div class="digi-passport-card">
            <div class="row align-items-center">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <div class="position-relative text-center mb-3" style="cursor: pointer; display: inline-block;" onclick="openCamera()">
                        @if($pasaporte && isset($pasaporte->foto_usuario) && $pasaporte->foto_usuario)
                            <img id="userPhotoPreview" src="{{ asset($pasaporte->foto_usuario) }}" width="100" height="100" style="object-fit:cover; border-radius:50%; border: 3px solid #dde8ff; margin:0 auto; display:block;" alt="Foto">
                            <div id="userPhotoInitials" style="display:none;"></div>
                        @else
                            <div id="userPhotoInitials" class="digi-passport-photo d-flex align-items-center justify-content-center"
                                 style="background:#dde8ff;font-size:3rem;font-weight:800;color:#2558e0;margin:0 auto; width:100px; height:100px; border-radius:50%;">
                                {{ strtoupper(substr($nombreCompleto ?: ($user->correo_usuario ?? 'U'), 0, 1)) }}
                            </div>
                            <img id="userPhotoPreview" src="" width="100" height="100" style="object-fit:cover; border-radius:50%; border: 3px solid #dde8ff; margin:0 auto; display:none;" alt="Foto">
                        @endif
                        <div style="font-size: 11px; margin-top: 8px; color: var(--blue); font-weight: 600;">
                            <i class="mdi mdi-camera"></i> Tomar foto
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="digi-info-grid">
                        <div class="digi-info-item">
                            <span class="digi-info-label">Nombre completo</span>
                            <span class="digi-info-value">{{ $nombreCompleto ?: '—' }}</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Fecha de nacimiento</span>
                            <span class="digi-info-value">
                                {{ $pasaporte && $pasaporte->fecha_nacimiento ? \Carbon\Carbon::parse($pasaporte->fecha_nacimiento)->format('d / m / Y') : '—' }}
                            </span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">CURP</span>
                            <span class="digi-info-value" style="font-family:monospace;">{{ $pasaporte->curp ?? '—' }}</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Vigencia</span>
                            <span class="digi-info-value digi-info-value--highlight">
                                {{ $pasaporte && $pasaporte->vigencia ? \Carbon\Carbon::parse($pasaporte->vigencia)->format('d / m / Y') : '—' }}
                            </span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Correo</span>
                            <span class="digi-info-value">{{ $user->correo_usuario }}</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Tipo de acceso</span>
                            <span class="digi-info-value">{{ ucfirst(strtolower($user->tipo_acceso ?? 'Paciente')) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloque 2: Datos editables --}}
        <div class="digi-card">
            <div class="digi-card__header">
                <h3 class="digi-card__title">
                    <i class="mdi mdi-account-details"></i>
                    Datos de usuario
                </h3>
            </div>
            <div class="digi-card__body">
                @if(session('password_ok'))
                <div class="alert alert-success mb-4" style="border-radius:8px;padding:.75rem 1rem;background:#ecfdf5;border:1px solid #6ee7b7;color:#065f46;">
                    <i class="mdi mdi-check-circle"></i> {{ session('password_ok') }}
                </div>
                @endif

                <div class="row g-4">
                    @foreach ($campos as $key => $meta)
                        <div class="col-md-4">
                            <div class="digi-editable-field" data-field="{{ $key }}">
                                <span class="digi-info-label">{{ $meta['label'] }}</span>

                                {{-- View Mode --}}
                                <div class="digi-view-mode">
                                    <span class="digi-view-mode__value">{{ $meta['valor'] }}</span>
                                    @if($meta['editable'])
                                    <button type="button" class="digi-edit-btn edit-btn">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    @endif
                                </div>

                                @if($meta['editable'])
                                {{-- Edit Mode --}}
                                <div class="digi-edit-mode d-none">
                                    <input type="text" class="digi-form-control" value="{{ $meta['valor'] }}">
                                    <div class="digi-edit-actions">
                                        <button type="button" class="digi-btn digi-btn--outline digi-btn--sm cancel-btn">
                                            Cancelar
                                        </button>
                                        <button type="button" class="digi-btn digi-btn--primary digi-btn--sm save-btn">
                                            Guardar
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bloque 3: Seguridad --}}
        <div class="digi-card">
            <div class="digi-card__header">
                <h3 class="digi-card__title">
                    <i class="mdi mdi-shield-lock"></i>
                    Seguridad
                </h3>
            </div>
            <div class="digi-card__body">
                <p class="digi-page-subtitle mb-4">
                    Actualiza tu contraseña para mantener tu cuenta segura.
                </p>

                <form method="POST" action="{{ route('perfil.password') }}">
                @csrf
                @if($errors->has('password_actual'))
                <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:8px;padding:.6rem 1rem;margin-bottom:1rem;font-size:.85rem;color:#dc2626;">
                    <i class="mdi mdi-alert-circle-outline"></i> {{ $errors->first('password_actual') }}
                </div>
                @endif
                <div class="row g-4">
                    {{-- Contraseña actual --}}
                    <div class="col-md-4">
                        <label class="digi-form-label">Contraseña actual</label>
                        <div class="digi-password-field">
                            <input type="password" name="password_actual" class="digi-form-control digi-password-input" id="currentPassword">
                            <button type="button" class="digi-toggle-password toggle-password">
                                <i class="mdi mdi-eye-outline"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Nueva contraseña --}}
                    <div class="col-md-4">
                        <label class="digi-form-label">Nueva contraseña</label>
                        <div class="digi-password-field">
                            <input type="password" name="password_nuevo" class="digi-form-control digi-password-input" id="newPassword">
                            <button type="button" class="digi-toggle-password toggle-password">
                                <i class="mdi mdi-eye-outline"></i>
                            </button>
                        </div>
                        <div class="digi-strength-meter">
                            <div class="digi-strength-bar">
                                <div class="digi-strength-progress" id="passwordStrength"></div>
                            </div>
                            <span class="digi-strength-text" id="passwordText">Seguridad de contraseña</span>
                        </div>
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div class="col-md-4">
                        <label class="digi-form-label">Confirmar contraseña</label>
                        <div class="digi-password-field">
                            <input type="password" name="password_nuevo_confirmation" class="digi-form-control digi-password-input" id="confirmPassword">
                            <button type="button" class="digi-toggle-password toggle-password">
                                <i class="mdi mdi-eye-outline"></i>
                            </button>
                        </div>
                        @error('password_nuevo')
                        <small style="color:#dc2626;">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Botón --}}
                    <div class="col-12 text-end">
                        <button type="submit" class="digi-btn digi-btn--primary">
                            <i class="mdi mdi-check"></i>
                            Actualizar contraseña
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        {{-- Bloque 4: Historia clínica --}}
        @php
            $hf = $historiaClinica?->heredo_familiares ?? [];
        @endphp
        <div class="digi-card">
            <div class="digi-card__header">
                <h3 class="digi-card__title">
                    <i class="mdi mdi-file-document-multiple"></i>
                    Historia clínica
                </h3>
            </div>
            <div class="digi-card__body">

                @if(session('historia_ok'))
                    <div style="background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.5rem;color:#065f46;font-size:.9rem;">
                        <i class="mdi mdi-check-circle"></i> {{ session('historia_ok') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.5rem;color:#dc2626;font-size:.9rem;">
                        <i class="mdi mdi-alert-circle-outline"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('perfil.historia-clinica') }}">
                @csrf

                {{-- Perfil social y hábitos --}}
                <div class="digi-section-block">
                    <h4 class="digi-section-title">
                        <i class="mdi mdi-account-group"></i>
                        Perfil social y hábitos
                    </h4>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="digi-form-label">Ocupación</label>
                            <input class="digi-form-control" name="ocupacion" placeholder="Ej. Empleado administrativo"
                                value="{{ old('ocupacion', $historiaClinica->ocupacion ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Estado civil</label>
                            <select class="digi-form-select" name="estado_civil">
                                <option value="">Seleccionar</option>
                                @foreach (['Soltero', 'Casado', 'Divorciado', 'Viudo', 'Unión libre'] as $opt)
                                    <option {{ old('estado_civil', $historiaClinica->estado_civil ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Escolaridad</label>
                            <select class="digi-form-select" name="escolaridad">
                                <option value="">Seleccionar</option>
                                @foreach (['Sin estudios', 'Primaria', 'Secundaria', 'Bachillerato', 'Licenciatura', 'Posgrado'] as $opt)
                                    <option {{ old('escolaridad', $historiaClinica->escolaridad ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Actividad física</label>
                            <select class="digi-form-select" name="actividad_fisica">
                                <option value="">Seleccionar</option>
                                @foreach (['Alta (5+ días/semana)', 'Moderada (2-3 veces/semana)', 'Baja (1 vez/semana)', 'Sedentario'] as $opt)
                                    <option {{ old('actividad_fisica', $historiaClinica->actividad_fisica ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Tabaquismo</label>
                            <select class="digi-form-select" name="tabaquismo">
                                <option value="">Seleccionar</option>
                                @foreach (['No', 'Sí', 'Ex-fumador'] as $opt)
                                    <option {{ old('tabaquismo', $historiaClinica->tabaquismo ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Consumo de alcohol</label>
                            <select class="digi-form-select" name="alcohol">
                                <option value="">Seleccionar</option>
                                @foreach (['No', 'Ocasional', 'Frecuente'] as $opt)
                                    <option {{ old('alcohol', $historiaClinica->alcohol ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Alimentación</label>
                            <select class="digi-form-select" name="alimentacion">
                                <option value="">Seleccionar</option>
                                @foreach (['Balanceada', 'Desequilibrada', 'Vegetariana', 'Vegana'] as $opt)
                                    <option {{ old('alimentacion', $historiaClinica->alimentacion ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="digi-divider">

                {{-- Antecedentes médicos --}}
                <div class="digi-section-block">
                    <h4 class="digi-section-title">
                        <i class="mdi mdi-family-tree"></i>
                        Antecedentes médicos
                    </h4>
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="digi-form-label">Antecedentes heredo-familiares <small style="font-weight:400;color:var(--text-muted);">(selecciona todos los que apliquen)</small></label>
                            <div class="row g-3 mt-1">
                                @foreach (['Diabetes', 'Hipertensión', 'Cáncer', 'Enfermedades cardíacas', 'Obesidad', 'Ninguno'] as $item)
                                    <div class="col-md-2 col-6">
                                        <div class="digi-check-card">
                                            <input type="checkbox" class="form-check-input" name="heredo_familiares[]"
                                                id="hf-{{ $loop->index }}" value="{{ $item }}"
                                                {{ in_array($item, old('heredo_familiares', $hf)) ? 'checked' : '' }}>
                                            <label for="hf-{{ $loop->index }}">{{ $item }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Antecedentes personales patológicos</label>
                            <input class="digi-form-control" name="personales_patologicos" placeholder="Ej. Ninguno, Asma..."
                                value="{{ old('personales_patologicos', $historiaClinica->personales_patologicos ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Antecedentes personales no patológicos</label>
                            <input class="digi-form-control" name="personales_no_patologicos" placeholder="Ej. Vacunas completas..."
                                value="{{ old('personales_no_patologicos', $historiaClinica->personales_no_patologicos ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Enfermedades previas</label>
                            <input class="digi-form-control" name="enfermedades_previas" placeholder="Ej. Varicela (infancia)..."
                                value="{{ old('enfermedades_previas', $historiaClinica->enfermedades_previas ?? '') }}">
                        </div>
                    </div>
                </div>

                <hr class="digi-divider">

                {{-- Alertas de seguridad --}}
                <div class="digi-section-block">
                    <h4 class="digi-section-title digi-section-title--danger">
                        <i class="mdi mdi-alert-circle"></i>
                        Alertas de seguridad
                    </h4>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="digi-form-label">Alergias</label>
                            <textarea class="digi-form-control" name="alergias" rows="3" placeholder="Ej. Penicilina, mariscos...">{{ old('alergias', $historiaClinica->alergias ?? '') }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="digi-form-label">Cirugías</label>
                            <textarea class="digi-form-control" name="cirugias" rows="3" placeholder="Ej. Apendicectomía 2018, ninguna...">{{ old('cirugias', $historiaClinica->cirugias ?? '') }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="digi-form-label">Medicamentos actuales</label>
                            <textarea class="digi-form-control" name="medicamentos" rows="3" placeholder="Ej. Metformina 500mg diaria...">{{ old('medicamentos', $historiaClinica->medicamentos ?? '') }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="digi-form-label">Intolerancias</label>
                            <textarea class="digi-form-control" name="intolerancias" rows="3" placeholder="Ej. Lactosa, gluten...">{{ old('intolerancias', $historiaClinica->intolerancias ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="digi-divider">

                {{-- Estado general --}}
                <div class="digi-section-block">
                    <h4 class="digi-section-title">
                        <i class="mdi mdi-heart-pulse"></i>
                        Estado general
                    </h4>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="digi-form-label">Peso (kg)</label>
                            <input type="number" class="digi-form-control" name="peso" id="campoPeso"
                                min="20" max="300" step="0.1" placeholder="Ej. 78"
                                value="{{ old('peso', $historiaClinica->peso ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">Altura (m)</label>
                            <input type="number" class="digi-form-control" name="altura" id="campoAltura"
                                min="0.5" max="2.5" step="0.01" placeholder="Ej. 1.75"
                                value="{{ old('altura', $historiaClinica->altura ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="digi-form-label">IMC <small style="font-weight:400;color:var(--text-muted);">(calculado automáticamente)</small></label>
                            <input type="text" class="digi-form-control" id="campoImc" name="imc" readonly
                                placeholder="—" style="background:var(--navy);cursor:default;"
                                value="{{ $historiaClinica && $historiaClinica->imc ? $historiaClinica->imc : '' }}">
                        </div>
                    </div>
                </div>

                {{-- Botón guardar --}}
                <div class="text-end mt-5">
                    <button type="submit" class="digi-btn digi-btn--primary">
                        <i class="mdi mdi-content-save"></i>
                        Guardar historia clínica
                    </button>
                </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Modal de Cámara --}}
    <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-camera"></i> Tomar Fotografía</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <video id="cameraStream" autoplay playsinline style="width: 100%; max-width: 400px; border-radius: 8px; background: #000; transform: scaleX(-1);"></video>
                    <canvas id="cameraCanvas" style="display:none;"></canvas>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnCapture"><i class="mdi mdi-camera-iris"></i> Capturar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // IMC auto-calculation
            const campoPeso   = document.getElementById('campoPeso');
            const campoAltura = document.getElementById('campoAltura');
            const campoImc    = document.getElementById('campoImc');

            function calcularImc() {
                const peso   = parseFloat(campoPeso.value);
                const altura = parseFloat(campoAltura.value);
                if (!peso || !altura || altura <= 0) { campoImc.value = ''; return; }
                const imc = peso / (altura * altura);
                let categoria = '';
                if      (imc < 18.5) categoria = 'Bajo peso';
                else if (imc < 25)   categoria = 'Normal';
                else if (imc < 30)   categoria = 'Sobrepeso';
                else                 categoria = 'Obesidad';
                campoImc.value = imc.toFixed(1) + ' (' + categoria + ')';
            }

            if (campoPeso && campoAltura) {
                campoPeso.addEventListener('input', calcularImc);
                campoAltura.addEventListener('input', calcularImc);
            }

            // Heredo-familiares: Ninguno deselects others; others deselect Ninguno
            const hfCheckboxes = document.querySelectorAll('input[name="heredo_familiares[]"]');
            const ninguno = [...hfCheckboxes].find(cb => cb.value === 'Ninguno');

            hfCheckboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    if (cb === ninguno && cb.checked) {
                        hfCheckboxes.forEach(other => { if (other !== ninguno) { other.checked = false; } });
                    } else if (cb !== ninguno && cb.checked) {
                        ninguno.checked = false;
                    }
                });
            });

            // Editable fields
            document.querySelectorAll(".digi-editable-field").forEach(field => {
                const editBtn = field.querySelector(".edit-btn");
                const cancelBtn = field.querySelector(".cancel-btn");
                const saveBtn = field.querySelector(".save-btn");
                const viewMode = field.querySelector(".digi-view-mode");
                const editMode = field.querySelector(".digi-edit-mode");
                const input = field.querySelector("input");

                editBtn.addEventListener("click", () => {
                    viewMode.classList.add("d-none");
                    editMode.classList.remove("d-none");
                    input.focus();
                });

                cancelBtn.addEventListener("click", () => {
                    editMode.classList.add("d-none");
                    viewMode.classList.remove("d-none");
                });

                saveBtn.addEventListener("click", () => {
                    const newValue = input.value.trim();
                    const campo    = field.dataset.field;

                    fetch('{{ route("perfil.campo") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ campo, valor: newValue })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            viewMode.querySelector(".digi-view-mode__value").textContent = newValue;
                            editMode.classList.add("d-none");
                            viewMode.classList.remove("d-none");
                        }
                    })
                    .catch(() => alert('Error al guardar'));
                });
            });

            // Toggle password visibility
            document.querySelectorAll(".toggle-password").forEach(btn => {
                btn.addEventListener("click", function() {
                    const input = this.closest(".digi-password-field").querySelector("input");
                    const icon = this.querySelector("i");

                    if (input.type === "password") {
                        input.type = "text";
                        icon.classList.replace("mdi-eye-outline", "mdi-eye-off-outline");
                    } else {
                        input.type = "password";
                        icon.classList.replace("mdi-eye-off-outline", "mdi-eye-outline");
                    }
                });
            });

            // Password strength meter
            const newPassword = document.getElementById("newPassword");
            const strengthBar = document.getElementById("passwordStrength");
            const strengthText = document.getElementById("passwordText");

            if (newPassword) {
                newPassword.addEventListener("input", function() {
                    let value = this.value;
                    let strength = 0;

                    if (value.length > 6) strength++;
                    if (value.match(/[A-Z]/)) strength++;
                    if (value.match(/[0-9]/)) strength++;
                    if (value.match(/[^A-Za-z0-9]/)) strength++;

                    const levels = ["Muy débil", "Débil", "Regular", "Buena", "Fuerte"];
                    const classes = ["", "weak", "regular", "good", "strong"];

                    strengthBar.className = "digi-strength-progress " + classes[strength];
                    strengthBar.style.width = (strength * 25) + "%";
                    strengthText.textContent = "Seguridad: " + (levels[strength] || "Muy débil");
                });
            }
        });

        // ── Cámara WebRTC ──
        let videoStream = null;
        let cameraModalInstance = null;
        const cameraModalEl = document.getElementById('cameraModal');
        const videoEl = document.getElementById('cameraStream');
        const canvasEl = document.getElementById('cameraCanvas');
        const btnCapture = document.getElementById('btnCapture');

        // Inicializar el modal de bootstrap cuando esté disponible
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof bootstrap !== 'undefined') {
                cameraModalInstance = new bootstrap.Modal(cameraModalEl);
            }
        });

        // Detener la cámara al cerrar el modal
        cameraModalEl.addEventListener('hidden.bs.modal', function () {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }
        });

        window.openCamera = function() {
            if (!cameraModalInstance) {
                if (typeof bootstrap === 'undefined') {
                    alert("Error: Bootstrap no está cargado.");
                    return;
                }
                cameraModalInstance = new bootstrap.Modal(cameraModalEl);
            }
            cameraModalInstance.show();

            // Verificar si el navegador soporta WebRTC / Contexto Seguro (HTTPS o localhost)
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                console.error("navigator.mediaDevices es undefined. Esto suele ocurrir si no usas HTTPS.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Cámara no disponible',
                    text: 'El navegador bloquea el acceso a la cámara porque la conexión no es segura (HTTPS) o tu dispositivo no tiene cámara.',
                    confirmButtonColor: '#2563eb'
                });
                cameraModalInstance.hide();
                return;
            }

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(stream => {
                    videoStream = stream;
                    videoEl.srcObject = stream;
                    videoEl.play();
                })
                .catch(err => {
                    console.error('Error accediendo a la cámara:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Sin acceso a la cámara',
                        text: 'Asegúrate de dar permisos a tu navegador para usar la cámara. Error: ' + err.message,
                        confirmButtonColor: '#2563eb'
                    });
                    cameraModalInstance.hide();
                });
        };

        btnCapture.addEventListener('click', () => {
            if (!videoStream) return;
            
            // Dibujar video en el canvas
            canvasEl.width = videoEl.videoWidth;
            canvasEl.height = videoEl.videoHeight;
            const ctx = canvasEl.getContext('2d');
            
            // Reflejar la imagen ya que la cámara frontal suele estar en espejo
            ctx.translate(canvasEl.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(videoEl, 0, 0, canvasEl.width, canvasEl.height);
            
            // Convertir a blob (jpeg)
            canvasEl.toBlob(blob => {
                if (!blob) return;
                uploadBlob(blob);
                cameraModalInstance.hide();
            }, 'image/jpeg', 0.9);
        });

        function uploadBlob(blob) {
            const formData = new FormData();
            formData.append('foto', blob, 'camara_' + Date.now() + '.jpg');
            formData.append('_token', '{{ csrf_token() }}');

            // Mostrar preview localmente
            const url = URL.createObjectURL(blob);
            const preview = document.getElementById('userPhotoPreview');
            const initials = document.getElementById('userPhotoInitials');
            
            if (initials) initials.style.display = 'none';
            preview.src = url;
            preview.style.display = 'block';

            // Subir al servidor
            fetch('{{ route("perfil.foto") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Foto actualizada!',
                        text: 'Tu foto de perfil se ha guardado correctamente.',
                        confirmButtonColor: '#2563eb',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'No se pudo actualizar la foto.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Ocurrió un error al subir la imagen.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    </script>
@endsection
