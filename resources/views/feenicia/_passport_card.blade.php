{{--
    Partial: _passport_card.blade.php
    Variables requeridas: $pasaporte, $user
    Incluir con: @include('feenicia._passport_card')
--}}

@if($pasaporte)
<div class="digi-passport-card">
    <div class="row align-items-center">

        {{-- Foto / Avatar --}}
        <div class="col-md-3 text-center mb-4 mb-md-0">
            <div class="digi-avatar-initials">
                {{ strtoupper(substr($pasaporte->nombres ?? '?', 0, 1)) }}{{ strtoupper(substr($pasaporte->apellido_pa ?? '', 0, 1)) }}
            </div>
            <div class="digi-passport-id mt-2">
                <i class="mdi mdi-card-account-details"></i>
                #PATS-{{ str_pad($pasaporte->id_pasaporte, 8, '0', STR_PAD_LEFT) }}
            </div>
            <div class="mt-2">
                @if($pasaporte->estatus === 'activo')
                    <span class="digi-badge digi-badge--success"><i class="mdi mdi-check-circle"></i> Vigente</span>
                @elseif($pasaporte->estatus === 'vencido')
                    <span class="digi-badge digi-badge--danger"><i class="mdi mdi-alert-circle"></i> Vencido</span>
                @else
                    <span class="digi-badge digi-badge--muted">{{ ucfirst($pasaporte->estatus) }}</span>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="col-md-9">
            <div class="digi-info-grid">
                <div class="digi-info-item">
                    <span class="digi-info-label">Nombre completo</span>
                    <span class="digi-info-value">
                        {{ $pasaporte->nombres }} {{ $pasaporte->apellido_pa }} {{ $pasaporte->apellido_ma }}
                    </span>
                </div>
                <div class="digi-info-item">
                    <span class="digi-info-label">CURP</span>
                    <span class="digi-info-value" style="font-family:monospace;font-size:.88rem;">
                        {{ $pasaporte->curp ?? '—' }}
                    </span>
                </div>
                <div class="digi-info-item">
                    <span class="digi-info-label">Fecha nacimiento</span>
                    <span class="digi-info-value">
                        {{ $pasaporte->fecha_nacimiento
                            ? \Carbon\Carbon::parse($pasaporte->fecha_nacimiento)->format('d/m/Y')
                            : '—' }}
                    </span>
                </div>
                <div class="digi-info-item">
                    <span class="digi-info-label">Correo</span>
                    <span class="digi-info-value">{{ $pasaporte->correo ?? $user->correo }}</span>
                </div>
                <div class="digi-info-item">
                    <span class="digi-info-label">Teléfono</span>
                    <span class="digi-info-value">{{ $pasaporte->telefono ?? '—' }}</span>
                </div>
                <div class="digi-info-item">
                    <span class="digi-info-label">Región / Zona</span>
                    <span class="digi-info-value">{{ $pasaporte->region ?? '—' }} · {{ $pasaporte->zona ?? '—' }}</span>
                </div>
                <div class="digi-info-item">
                    <span class="digi-info-label">Plan actual</span>
                    <span class="digi-info-value">
                        {{ ucfirst(strtolower($pasaporte->frecuencia_pago ?? '—')) }}
                        · ${{ number_format($pasaporte->valor_final_pasaporte ?? 0, 0) }} MXN
                    </span>
                </div>
                <div class="digi-info-item">
                    <span class="digi-info-label">Vigencia hasta</span>
                    <span class="digi-info-value" style="{{ $pasaporte->estatus === 'vencido' ? 'color:#dc2626;font-weight:600;' : 'color:#059669;' }}">
                        {{ $pasaporte->fecha_vencimiento_real
                            ? \Carbon\Carbon::parse($pasaporte->fecha_vencimiento_real)->format('d/m/Y')
                            : '—' }}
                        @if($pasaporte->estatus === 'activo' && $pasaporte->fecha_vencimiento_real)
                            <small style="color:#64748b;font-weight:400;">
                                ({{ max(0, now()->diffInDays(\Carbon\Carbon::parse($pasaporte->fecha_vencimiento_real), false)) }} días)
                            </small>
                        @endif
                    </span>
                </div>
            </div>

            {{-- Alerta de recargo --}}
            @if($pasaporte->meses_vencidos > 0)
            <div style="margin-top:1rem;padding:.75rem 1rem;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;font-size:.83rem;color:#991b1b;">
                <i class="mdi mdi-alert-circle"></i>
                <strong>Pasaporte vencido</strong> · Meses vencidos: <strong>{{ $pasaporte->meses_vencidos }}</strong>
                · Recargo acumulado: <strong>${{ number_format($pasaporte->recargo_acumulado, 2) }} MXN</strong>
                <br><small>El recargo se sumará automáticamente al seleccionar los meses a pagar.</small>
            </div>
            @endif
        </div>
    </div>
</div>
@else
<div class="digi-passport-card" style="text-align:center;padding:2rem;">
    <i class="mdi mdi-card-off-outline" style="font-size:3rem;color:#cbd5e1;display:block;margin-bottom:.75rem;"></i>
    <h4 style="font-family:'Syne',sans-serif;color:#1e3a5f;margin-bottom:.5rem;">No tienes un pasaporte activo</h4>
    <p style="color:#64748b;font-size:.88rem;">Realiza tu primer pago para activar tu pasaporte PATS.</p>
</div>
@endif