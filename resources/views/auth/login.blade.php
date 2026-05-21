<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login | PATS</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/horizontal-layout/style.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/general.css') }}">
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="main-panel w-100">
                <div class="content-wrapper d-flex justify-content-center align-items-center min-vh-100 auth">
                    <div class="row w-100 justify-content-center">
                        <div class="col-lg-4">

                            <div class="auth-form-light p-5 radius-lg shadow-sm">

                                {{-- Logo --}}
                                <div class="brand-logo text-center mb-4">
                                    <img src="{{ asset('images/logof.png') }}" alt="logo" class="radius-lg">
                                </div>

                                {{-- Título --}}
                                <h4 class="text-title text-center mb-2">Bienvenido</h4>
                                <p class="text-normal text-center text-muted mb-4">Inicia sesión para continuar</p>

                                {{-- Errores --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 p-3 rounded">
                                        <i class="mdi mdi-alert-circle" style="font-size:1.2rem;"></i>
                                        <span>{{ $errors->first() }}</span>
                                    </div>
                                @endif

                                {{-- Mensaje de sesión expirada --}}
                                @if (session('status'))
                                    <div class="alert alert-info mb-3 p-3 rounded">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login.post') }}">
                                    @csrf

                                    {{-- Email --}}
                                    <div class="form-group mb-3">
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control form-control-lg radius-lg text-normal @error('email') is-invalid @enderror"
                                            placeholder="Correo electrónico"
                                            value="{{ old('email') }}"
                                            required
                                            autofocus>
                                    </div>

                                    {{-- Password --}}
                                    <div class="form-group mb-4">
                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control form-control-lg radius-lg text-normal"
                                            placeholder="Contraseña"
                                            required>
                                    </div>

                                    {{-- Botón --}}
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg radius-lg text-small">
                                            INICIAR SESIÓN
                                        </button>
                                    </div>

                                    {{-- Opciones --}}
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="d-flex align-items-center gap-2 text-small text-muted">
                                            <input type="checkbox" name="remember" class="form-check-input"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            Mantener sesión activa
                                        </label>

                                        <a href="/" class="text-small text-primary text-decoration-none">
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    </div>

                                </form>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>

</body>
</html>