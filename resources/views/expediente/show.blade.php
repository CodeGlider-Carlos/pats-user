<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente Médico — PATS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue:    #2563eb;
            --navy:    #1e3a5f;
            --bg:      #f1f5f9;
            --white:   #ffffff;
            --border:  #e2e8f0;
            --text:    #1e293b;
            --muted:   #64748b;
            --danger:  #dc2626;
            --success: #10b981;
            --warning: #f59e0b;
            --radius:  12px;
            --shadow:  0 4px 24px rgba(0,0,0,.08);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 24px 16px 48px;
        }

        /* ── Header ── */
        .exp-header {
            max-width: 800px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .exp-header__logo {
            height: 36px;
            filter: none;
        }

        .exp-header__badge {
            background: var(--blue);
            color: #fff;
            font-size: .75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .exp-header__divider {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Card layout ── */
        .exp-card {
            max-width: 800px;
            margin: 0 auto 20px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .exp-card__header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            color: #fff;
        }

        .exp-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,.4);
            object-fit: cover;
            flex-shrink: 0;
            background: rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
        }

        .exp-photo img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .exp-patient__name {
            font-family: 'Syne', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .exp-patient__id {
            font-size: .85rem;
            opacity: .8;
            margin-top: 4px;
        }

        .exp-patient__status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
        }

        .exp-patient__status--active {
            background: rgba(16,185,129,.2);
            color: #6ee7b7;
        }

        .exp-patient__status--inactive {
            background: rgba(220,38,38,.2);
            color: #fca5a5;
        }

        .exp-card__body {
            padding: 24px;
        }

        /* ── Section title ── */
        .exp-section {
            margin-bottom: 24px;
        }

        .exp-section__title {
            font-family: 'Syne', sans-serif;
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--blue);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #eff6ff;
        }

        /* ── Grid info ── */
        .exp-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        @media (max-width: 500px) {
            .exp-grid { grid-template-columns: 1fr; }
            .exp-grid--3 { grid-template-columns: 1fr 1fr; }
        }

        .exp-grid--3 { grid-template-columns: repeat(3, 1fr); }

        .exp-field {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
        }

        .exp-field__label {
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
            margin-bottom: 3px;
        }

        .exp-field__value {
            font-size: .95rem;
            font-weight: 500;
            color: var(--text);
        }

        .exp-field__value--empty {
            color: var(--muted);
            font-style: italic;
            font-size: .85rem;
        }

        /* ── Alert tags ── */
        .exp-alert-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .exp-alert-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 600;
        }

        .exp-alert-tag--danger {
            background: #fee2e2;
            color: var(--danger);
            border: 1px solid #fca5a5;
        }

        .exp-alert-tag--warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .exp-alert-tag--info {
            background: #eff6ff;
            color: var(--blue);
            border: 1px solid #bfdbfe;
        }

        .exp-alert-tag--empty {
            background: #f1f5f9;
            color: var(--muted);
            border: 1px solid var(--border);
        }

        /* ── IMC badge ── */
        .exp-imc {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .exp-imc__number {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--blue);
        }

        .exp-imc__label {
            font-size: .8rem;
            color: var(--muted);
        }

        .exp-imc__classification {
            font-size: .82rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 999px;
        }

        /* ── Footer ── */
        .exp-footer {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            font-size: .75rem;
            color: var(--muted);
            padding-top: 8px;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .exp-card { box-shadow: none; border: 1px solid #ccc; }
            .exp-no-print { display: none !important; }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <header class="exp-header">
        <img src="{{ asset('images/PATS_LOGO.png') }}" alt="PATS" class="exp-header__logo">
        <span class="exp-header__badge"><i class="mdi mdi-qrcode-scan"></i> Expediente Médico</span>
        <div class="exp-header__divider"></div>
        <span style="font-size:.8rem;color:var(--muted);">{{ now()->format('d/m/Y H:i') }}</span>
    </header>

    {{-- Tarjeta principal del paciente --}}
    <div class="exp-card">
        <div class="exp-card__header">
            <div class="exp-photo">
                @php $fotoUrl = route('expediente.foto', $pasaporte->token_qr); @endphp
                <img src="{{ $fotoUrl }}" alt="Foto"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div style="display:none;width:100%;height:100%;border-radius:50%;background:rgba(255,255,255,.15);align-items:center;justify-content:center;font-size:2rem;font-weight:700;">
                    {{ strtoupper(substr($pasaporte->nombres, 0, 1)) }}
                </div>
            </div>
            <div>
                <div class="exp-patient__name">
                    {{ $pasaporte->nombres }} {{ $pasaporte->apellido_pa }} {{ $pasaporte->apellido_ma }}
                </div>
                <div class="exp-patient__id">
                    <i class="mdi mdi-identifier"></i>
                    #PATS-{{ str_pad($pasaporte->id_pasaporte, 8, '0', STR_PAD_LEFT) }}
                    &nbsp;·&nbsp;
                    CURP: {{ $pasaporte->curp }}
                </div>
                @if ($pasaporte->estatus === 'activo' || $pasaporte->estatus === 'ACTIVO')
                    <span class="exp-patient__status exp-patient__status--active">
                        <i class="mdi mdi-check-circle"></i> Pasaporte vigente
                    </span>
                @else
                    <span class="exp-patient__status exp-patient__status--inactive">
                        <i class="mdi mdi-alert-circle"></i> Pasaporte {{ $pasaporte->estatus }}
                    </span>
                @endif
            </div>
        </div>

        <div class="exp-card__body">

            {{-- Datos generales --}}
            <div class="exp-section">
                <div class="exp-section__title">
                    <i class="mdi mdi-account-details"></i> Datos generales
                </div>
                <div class="exp-grid">
                    <div class="exp-field">
                        <div class="exp-field__label">Fecha de nacimiento</div>
                        <div class="exp-field__value">
                            {{ \Carbon\Carbon::parse($pasaporte->fecha_nacimiento)->format('d/m/Y') }}
                            <span style="color:var(--muted);font-size:.85rem;">({{ $edad }} años)</span>
                        </div>
                    </div>
                    <div class="exp-field">
                        <div class="exp-field__label">Teléfono de contacto</div>
                        <div class="exp-field__value">{{ $pasaporte->telefono ?: '—' }}</div>
                    </div>
                    @if($historiaClinica)
                    <div class="exp-field">
                        <div class="exp-field__label">Estado civil</div>
                        <div class="exp-field__value">{{ ucfirst(strtolower($historiaClinica->estado_civil ?? '—')) }}</div>
                    </div>
                    <div class="exp-field">
                        <div class="exp-field__label">Ocupación</div>
                        <div class="exp-field__value">{{ $historiaClinica->ocupacion ?: '—' }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if ($historiaClinica)

            {{-- Alertas médicas críticas --}}
            <div class="exp-section">
                <div class="exp-section__title" style="color:#dc2626;border-bottom-color:#fee2e2;">
                    <i class="mdi mdi-alert-circle"></i> Alertas médicas
                </div>
                <div style="display:grid;gap:14px;">

                    {{-- Alergias --}}
                    <div>
                        <div style="font-size:.78rem;font-weight:600;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">
                            <i class="mdi mdi-allergy"></i> Alergias
                        </div>
                        <div class="exp-alert-list">
                            @if ($historiaClinica->alergias)
                                @foreach (array_filter(array_map('trim', explode(',', $historiaClinica->alergias))) as $alergia)
                                    <span class="exp-alert-tag exp-alert-tag--danger">
                                        <i class="mdi mdi-close-circle"></i> {{ $alergia }}
                                    </span>
                                @endforeach
                            @else
                                <span class="exp-alert-tag exp-alert-tag--empty">Sin alergias registradas</span>
                            @endif
                        </div>
                    </div>

                    {{-- Medicamentos --}}
                    <div>
                        <div style="font-size:.78rem;font-weight:600;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">
                            <i class="mdi mdi-pill"></i> Medicamentos actuales
                        </div>
                        <div class="exp-alert-list">
                            @if ($historiaClinica->medicamentos)
                                @foreach (array_filter(array_map('trim', explode(',', $historiaClinica->medicamentos))) as $med)
                                    <span class="exp-alert-tag exp-alert-tag--warning">
                                        <i class="mdi mdi-pill"></i> {{ $med }}
                                    </span>
                                @endforeach
                            @else
                                <span class="exp-alert-tag exp-alert-tag--empty">Sin medicamentos registrados</span>
                            @endif
                        </div>
                    </div>

                    {{-- Intolerancias --}}
                    <div>
                        <div style="font-size:.78rem;font-weight:600;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">
                            <i class="mdi mdi-food-off"></i> Intolerancias
                        </div>
                        <div class="exp-alert-list">
                            @if ($historiaClinica->intolerancias)
                                @foreach (array_filter(array_map('trim', explode(',', $historiaClinica->intolerancias))) as $int)
                                    <span class="exp-alert-tag exp-alert-tag--warning">
                                        <i class="mdi mdi-food-off"></i> {{ $int }}
                                    </span>
                                @endforeach
                            @else
                                <span class="exp-alert-tag exp-alert-tag--empty">Sin intolerancias registradas</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            {{-- Medidas físicas --}}
            @if ($historiaClinica->peso || $historiaClinica->altura)
            <div class="exp-section">
                <div class="exp-section__title">
                    <i class="mdi mdi-human-male-height"></i> Medidas físicas
                </div>
                <div class="exp-grid exp-grid--3">
                    @if ($historiaClinica->peso)
                    <div class="exp-field">
                        <div class="exp-field__label">Peso</div>
                        <div class="exp-field__value">{{ $historiaClinica->peso }} kg</div>
                    </div>
                    @endif
                    @if ($historiaClinica->altura)
                    <div class="exp-field">
                        <div class="exp-field__label">Altura</div>
                        <div class="exp-field__value">{{ $historiaClinica->altura }} m</div>
                    </div>
                    @endif
                    @if ($historiaClinica->imc)
                    <div class="exp-field">
                        <div class="exp-field__label">IMC</div>
                        <div class="exp-field__value">
                            {{ $historiaClinica->imc }}
                            @php
                                $imc = (float) $historiaClinica->imc;
                                [$imcLabel, $imcColor] = match(true) {
                                    $imc < 18.5 => ['Bajo peso', '#3b82f6'],
                                    $imc < 25   => ['Normal', '#10b981'],
                                    $imc < 30   => ['Sobrepeso', '#f59e0b'],
                                    default     => ['Obesidad', '#dc2626'],
                                };
                            @endphp
                            <span style="font-size:.75rem;font-weight:600;color:{{ $imcColor }};margin-left:4px;">
                                {{ $imcLabel }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Antecedentes médicos --}}
            <div class="exp-section">
                <div class="exp-section__title">
                    <i class="mdi mdi-clipboard-pulse"></i> Antecedentes médicos
                </div>
                <div class="exp-grid">
                    <div class="exp-field">
                        <div class="exp-field__label">Antecedentes heredo-familiares</div>
                        <div class="exp-field__value">
                            @php
                                $heredoFam = $historiaClinica->heredo_familiares;
                                if (is_string($heredoFam)) $heredoFam = json_decode($heredoFam, true);
                                $heredoFam = is_array($heredoFam) ? array_filter($heredoFam) : [];
                            @endphp
                            @if ($heredoFam)
                                <div class="exp-alert-list" style="margin-top:4px;">
                                    @foreach ($heredoFam as $antec)
                                        <span class="exp-alert-tag exp-alert-tag--info" style="font-size:.78rem;">
                                            {{ $antec }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="exp-field__value--empty">Sin antecedentes registrados</span>
                            @endif
                        </div>
                    </div>
                    <div class="exp-field">
                        <div class="exp-field__label">Antecedentes personales patológicos</div>
                        <div class="exp-field__value">
                            {{ $historiaClinica->personales_patologicos ?: '—' }}
                        </div>
                    </div>
                    <div class="exp-field">
                        <div class="exp-field__label">Enfermedades previas</div>
                        <div class="exp-field__value">{{ $historiaClinica->enfermedades_previas ?: '—' }}</div>
                    </div>
                    <div class="exp-field">
                        <div class="exp-field__label">Cirugías previas</div>
                        <div class="exp-field__value">{{ $historiaClinica->cirugias ?: '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Hábitos --}}
            <div class="exp-section">
                <div class="exp-section__title">
                    <i class="mdi mdi-heart-pulse"></i> Hábitos y estilo de vida
                </div>
                <div class="exp-grid">
                    <div class="exp-field">
                        <div class="exp-field__label">Actividad física</div>
                        <div class="exp-field__value">{{ ucfirst(strtolower($historiaClinica->actividad_fisica ?? '—')) }}</div>
                    </div>
                    <div class="exp-field">
                        <div class="exp-field__label">Alimentación</div>
                        <div class="exp-field__value">{{ ucfirst(strtolower($historiaClinica->alimentacion ?? '—')) }}</div>
                    </div>
                    <div class="exp-field">
                        <div class="exp-field__label">Tabaquismo</div>
                        <div class="exp-field__value">{{ ucfirst(strtolower($historiaClinica->tabaquismo ?? '—')) }}</div>
                    </div>
                    <div class="exp-field">
                        <div class="exp-field__label">Alcohol</div>
                        <div class="exp-field__value">{{ ucfirst(strtolower($historiaClinica->alcohol ?? '—')) }}</div>
                    </div>
                </div>
            </div>

            @else
            <div style="text-align:center;padding:32px;color:var(--muted);">
                <i class="mdi mdi-clipboard-text-off" style="font-size:3rem;display:block;margin-bottom:8px;"></i>
                Este paciente aún no ha completado su historia clínica.
            </div>
            @endif

        </div>
    </div>

    {{-- Footer --}}
    <div class="exp-footer">
        <p>Este expediente fue generado por el sistema PATS al escanear el código QR del paciente.</p>
        <p style="margin-top:4px;">La información es confidencial y debe usarse exclusivamente para fines médicos.</p>
    </div>

</body>
</html>
