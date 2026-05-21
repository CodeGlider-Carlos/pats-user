<link rel="stylesheet" href="{{ asset('styles/navbar.css') }}">
<nav class="digi-navbar">
    {{-- TOP BAR --}}
    <div class="digi-topbar">
        <div class="digi-container">

            {{-- LOGO --}}
            <a href="{{ route('pasaporte') }}" class="digi-brand">
                <img src="{{ asset('images/logof.png') }}" width="150"  alt="">
            </a>

            {{-- RIGHT ACTIONS --}}
            <div class="digi-actions">

                {{-- CAMPANA --}}
                <div class="digi-dropdown">
                    <button class="digi-icon-btn" data-dropdown="notif">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        <span class="digi-badge">3</span>
                    </button>

                    <div class="digi-dropdown__panel" id="dropdown-notif">
                        <div class="digi-dropdown__header">
                            <span>Notificaciones</span>
                            <span class="digi-chip">3 nuevas</span>
                        </div>
                        <div class="digi-notif-list">
                            <a href="#" class="digi-notif-item digi-notif-item--blue">
                                <div class="digi-notif-item__icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <rect x="1" y="4" width="22" height="16" rx="2" />
                                        <line x1="1" y1="10" x2="23" y2="10" />
                                    </svg>
                                </div>
                                <div class="digi-notif-item__body">
                                    <p class="digi-notif-item__title">Pago registrado</p>
                                    <p class="digi-notif-item__desc">Tu pago fue procesado correctamente</p>
                                    <span class="digi-notif-item__time">Hace 5 min</span>
                                </div>
                            </a>
                            <a href="#" class="digi-notif-item digi-notif-item--green">
                                <div class="digi-notif-item__icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                </div>
                                <div class="digi-notif-item__body">
                                    <p class="digi-notif-item__title">Cita confirmada</p>
                                    <p class="digi-notif-item__desc">Cita con especialista confirmada</p>
                                    <span class="digi-notif-item__time">Hace 1 hora</span>
                                </div>
                            </a>
                            <a href="#" class="digi-notif-item digi-notif-item--amber">
                                <div class="digi-notif-item__icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                    </svg>
                                </div>
                                <div class="digi-notif-item__body">
                                    <p class="digi-notif-item__title">PATS por vencer</p>
                                    <p class="digi-notif-item__desc">Renovación próxima a vencer</p>
                                    <span class="digi-notif-item__time">Hace 2 horas</span>
                                </div>
                            </a>
                        </div>
                        <a href="#" class="digi-dropdown__footer">Ver todas las notificaciones →</a>
                    </div>
                </div>

                <div class="digi-divider-v"></div>

                {{-- USUARIO --}}
                <div class="digi-dropdown">
                    @php $navUser = auth()->user() ?? auth('pasaporte')->user(); @endphp
                    <button class="digi-user-btn" data-dropdown="user">
                        <div class="digi-avatar" style="background:#dde8ff;color:#2558e0;font-weight:700;font-size:14px;display:flex;align-items:center;justify-content:center;">
                            {{ strtoupper(substr($navUser->nombre_usuario ?? $navUser->nombre_paciente ?? $navUser->correo_usuario ?? 'U', 0, 1)) }}
                        </div>
                        <div class="digi-user-info d-none d-md-block">
                            <span class="digi-user-info__name">{{ $navUser->nombre_usuario ?? $navUser->nombre_paciente ?? $navUser->correo_usuario }}</span>
                        </div>
                        <svg class="digi-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </button>

                    <div class="digi-dropdown__panel digi-dropdown__panel--right" id="dropdown-user">
                        <div class="digi-dropdown__profile">
                            <div class="digi-avatar digi-avatar--lg" style="background:#dde8ff;color:#2558e0;font-weight:700;font-size:18px;display:flex;align-items:center;justify-content:center;">
                                {{ strtoupper(substr($navUser->nombre_usuario ?? $navUser->nombre_paciente ?? $navUser->correo_usuario ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="digi-dropdown__profile-name">{{ $navUser->nombre_usuario ?? $navUser->nombre_paciente ?? $navUser->correo_usuario }}</p>
                                <p class="digi-dropdown__profile-email">{{ $navUser->correo_usuario }}</p>
                            </div>
                        </div>
                        <div class="digi-dropdown__divider"></div>
                        <a href="/perfil" class="digi-dropdown__item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            Mi perfil
                        </a>
                         <a href="{{ route('agenda.index') }}" class="digi-dropdown__item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                           Agenda
                        </a>
                        <a href="/" class="digi-dropdown__item digi-dropdown__item--danger">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Cerrar sesión
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- BOTTOM NAV --}}
    <div class="digi-subnav">
        <div class="digi-container">
            <ul class="digi-nav">
                <li class="digi-nav__item">
                    <a href="{{ route('pasaporte') }}"
                        class="digi-nav__link {{ request()->routeIs('pasaporte') ? 'is-active' : '' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <line x1="2" y1="10" x2="22" y2="10" />
                        </svg>
                        Mi pasaporte
                    </a>
                </li>
                <li class="digi-nav__item">
                    <a href="{{ route('servicios') }}"
                        class="digi-nav__link {{ request()->routeIs('servicios') ? 'is-active' : '' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                        Mis servicios
                    </a>
                </li>
                <li class="digi-nav__item">
                    <a href="{{ route('pagos') }}"
                        class="digi-nav__link {{ request()->routeIs('pagos') ? 'is-active' : '' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" />
                            <line x1="1" y1="10" x2="23" y2="10" />
                        </svg>
                        Mis pagos
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- FAB MOBILE --}}
    <div class="digi-fab d-lg-none">
        <div class="digi-fab__menu" id="digiFabMenu">
            <a href="{{ route('pasaporte') }}"
                class="digi-fab__item {{ request()->routeIs('pasaporte') ? 'is-active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2" />
                    <line x1="2" y1="10" x2="22" y2="10" />
                </svg>
                <span>Pasaporte</span>
            </a>
            <a href="{{ route('servicios') }}"
                class="digi-fab__item {{ request()->routeIs('servicios*') ? 'is-active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                </svg>
                <span>Servicios</span>
            </a>
            <a href="{{ route('pagos') }}"
                class="digi-fab__item {{ request()->routeIs('pagos*') ? 'is-active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" />
                    <line x1="1" y1="10" x2="23" y2="10" />
                </svg>
                <span>Pagos</span>
            </a>
        </div>
        <button class="digi-fab__btn" id="digiFabToggle">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
        </button>
    </div>

    {{-- MOBILE BOTTOM TAB BAR (solo visible en móvil, reemplaza subnav + FAB) --}}
    <div class="digi-bottom-nav">
        <a href="{{ route('pasaporte') }}"
            class="digi-bottom-nav__item {{ request()->routeIs('pasaporte') ? 'is-active' : '' }}">
            <div class="digi-bottom-nav__icon-wrap">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
            </div>
            <span>Pasaporte</span>
        </a>
        <a href="{{ route('servicios') }}"
            class="digi-bottom-nav__item {{ request()->routeIs('servicios') || request()->routeIs('hospitales.index') || request()->routeIs('especialidades.*') || request()->routeIs('atencion.*') || request()->routeIs('estudios.*') || request()->routeIs('farmacia.*') || request()->routeIs('rayos.*') ? 'is-active' : '' }}">
            <div class="digi-bottom-nav__icon-wrap">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>
            <span>Servicios</span>
        </a>
        <a href="{{ route('agenda.index') }}"
            class="digi-bottom-nav__item {{ request()->routeIs('agenda.*') ? 'is-active' : '' }}">
            <div class="digi-bottom-nav__icon-wrap">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <span>Agenda</span>
        </a>
        <a href="{{ route('pagos') }}"
            class="digi-bottom-nav__item {{ request()->routeIs('pagos') ? 'is-active' : '' }}">
            <div class="digi-bottom-nav__icon-wrap">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <span>Pagos</span>
        </a>
        <a href="{{ route('perfil') }}"
            class="digi-bottom-nav__item {{ request()->routeIs('perfil') ? 'is-active' : '' }}">
            <div class="digi-bottom-nav__icon-wrap">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <span>Perfil</span>
        </a>
    </div>

</nav>

<script>
    /**
     * DIGI NAVBAR — JS
     * Dropdowns + FAB toggle
     */
    document.addEventListener('DOMContentLoaded', () => {

        // ---- DROPDOWNS ----
        document.querySelectorAll('[data-dropdown]').forEach(trigger => {
            const targetId = trigger.getAttribute('data-dropdown');
            const panel = document.getElementById(`dropdown-${targetId}`);
            const parent = trigger.closest('.digi-dropdown');

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();

                // Close all others
                document.querySelectorAll('.digi-dropdown.is-open').forEach(d => {
                    if (d !== parent) d.classList.remove('is-open');
                });

                parent.classList.toggle('is-open');
            });
        });

        // Click outside to close dropdowns
        document.addEventListener('click', () => {
            document.querySelectorAll('.digi-dropdown.is-open').forEach(d => {
                d.classList.remove('is-open');
            });
        });

        // ---- FAB TOGGLE ----
        const fabToggle = document.getElementById('digiFabToggle');
        const fab = fabToggle?.closest('.digi-fab');

        fabToggle?.addEventListener('click', (e) => {
            e.stopPropagation();
            fab?.classList.toggle('is-open');
        });

        document.addEventListener('click', (e) => {
            if (!fab?.contains(e.target)) {
                fab?.classList.remove('is-open');
            }
        });

        // Close FAB on item click
        document.querySelectorAll('.digi-fab__item').forEach(item => {
            item.addEventListener('click', () => fab?.classList.remove('is-open'));
        });

    });
</script>
