<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard')</title>

    {{-- CSRF para Axios --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Axios desde CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    {{-- Fuentes modernas --}}
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@600;700;800&display=swap"
        rel="stylesheet">

    {{-- Iconos --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/horizontal-layout/style.css') }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">

    {{-- Tus estilos existentes --}}
    <link rel="stylesheet" href="{{ asset('styles/general.css') }}">

    {{-- FullCalendar --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

</head>

<body>
    <div class="container-scroller">

        {{-- TU NAVBAR ORIGINAL --}}
        @include('partials.navbar')

        <div class="container-fluid page-body-wrapper">
            {{-- Main panel con fondo moderno --}}
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>

                {{-- TU FOOTER ORIGINAL --}}
                @include('partials.footer')
            </div>
        </div>

    </div>

    {{-- Scripts --}}
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>

    {{-- Script de la tarjeta de crédito --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.getElementById('creditCard');
            const cardFront = document.getElementById('cardFront');
            const brand = document.getElementById('cardBrand');

            if (card) {
                document.getElementById('nameInput')?.addEventListener('input', e => {
                    document.getElementById('cardName').innerText =
                        e.target.value.toUpperCase() || 'NOMBRE APELLIDO';
                });

                document.getElementById('cardInput')?.addEventListener('input', e => {
                    let value = e.target.value.replace(/\D/g, '').substring(0, 16);
                    value = value.replace(/(.{4})/g, '$1 ').trim();
                    e.target.value = value;

                    document.getElementById('cardNumber').innerText =
                        value || '•••• •••• •••• ••••';

                    cardFront.className = 'card-face card-front';

                    if (/^4/.test(value)) {
                        cardFront.classList.add('visa');
                        brand.innerText = 'VISA';
                    } else if (/^5[1-5]/.test(value)) {
                        cardFront.classList.add('mastercard');
                        brand.innerText = 'MASTERCARD';
                    } else if (/^3[47]/.test(value)) {
                        cardFront.classList.add('amex');
                        brand.innerText = 'AMEX';
                    }
                });

                document.getElementById('expiryInput')?.addEventListener('input', e => {
                    let value = e.target.value.replace(/\D/g, '').substring(0, 4);
                    if (value.length >= 3) {
                        value = value.substring(0, 2) + '/' + value.substring(2);
                    }
                    e.target.value = value;
                    document.getElementById('cardExpiry').innerText = value || 'MM/AA';
                });

                document.getElementById('cvvInput')?.addEventListener('focus', () => {
                    card.classList.add('flip');
                });

                document.getElementById('cvvInput')?.addEventListener('blur', () => {
                    card.classList.remove('flip');
                });

                document.getElementById('cvvInput')?.addEventListener('input', e => {
                    document.getElementById('cardCvv').innerText =
                        e.target.value.replace(/\D/g, '').substring(0, 4) || '***';
                });
            }
        });
    </script>

    {{-- Script del calendario --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            if (calendarEl) {
                window.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: window.innerWidth < 768 ? 'listWeek' : 'dayGridMonth',
                    locale: 'es',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listWeek'
                    },
                    events: [{
                        title: 'Cita - Cardiología',
                        start: '2026-02-20'
                    }],
                    eventClick: function(info) {
                        const modal = document.getElementById('citaDetalle');
                        if (modal) {
                            modal.classList.remove('d-none');
                            if (window.innerWidth < 768) {
                                modal.scrollIntoView({
                                    behavior: 'smooth'
                                });
                            }
                        }
                    }
                });

                window.calendar.render();

                window.addEventListener('resize', function() {
                    if (window.calendar) {
                        if (window.innerWidth < 768) {
                            window.calendar.changeView('listWeek');
                        } else {
                            window.calendar.changeView('dayGridMonth');
                        }
                    }
                });
            }
        });

        document.addEventListener('shown.bs.tab', function(event) {
            if (event.target.getAttribute('data-bs-target') === '#agenda') {
                if (window.calendar) {
                    window.calendar.updateSize();
                }
            }
        });
    </script>

    {{-- Script del FAB --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fab = document.getElementById("fabToggle");
            const container = document.querySelector(".fab-container");

            if (fab) {
                fab.addEventListener("click", function() {
                    container.classList.toggle("open");
                });
            }
        });
    </script>

    {{-- Función para cerrar detalle de cita --}}
    <script>
        function cerrarDetalleCita() {
            document.getElementById('citaDetalle').classList.add('d-none');
        }
    </script>

    @stack('scripts')

    @include('partials.chatbot')
</body>

</html>
