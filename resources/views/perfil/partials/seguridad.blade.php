<div class="card mb-4 radius-lg shadow-sm">

    <div class="card-header radius-lg text-subtitle">
        Seguridad
    </div>

    <div class="card-body">

        <p class="text-small text-muted mb-4">
            Actualiza tu contraseña para mantener tu cuenta segura.
        </p>

        <form class="row g-4">

            {{-- CONTRASEÑA ACTUAL --}}
            <div class="col-md-4">

                <label class="passport-label">
                    Contraseña actual
                </label>

                <div class="position-relative">
                    <input type="password"
                           class="form-control radius-lg text-normal pe-5"
                           id="currentPassword">

                    <button type="button"
                            class="toggle-password btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 text-muted">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                </div>

            </div>

            {{-- NUEVA --}}
            <div class="col-md-4">

                <label class="passport-label">
                    Nueva contraseña
                </label>

                <div class="position-relative">
                    <input type="password"
                           class="form-control radius-lg text-normal pe-5"
                           id="newPassword">

                    <button type="button"
                            class="toggle-password btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 text-muted">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                </div>

                {{-- Indicador fuerza --}}
                <div class="mt-2">
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar" id="passwordStrength"
                             style="width:0%"></div>
                    </div>
                    <small class="text-small text-muted" id="passwordText">
                        Seguridad de contraseña
                    </small>
                </div>

            </div>

            {{-- CONFIRMAR --}}
            <div class="col-md-4">

                <label class="passport-label">
                    Confirmar contraseña
                </label>

                <div class="position-relative">
                    <input type="password"
                           class="form-control radius-lg text-normal pe-5"
                           id="confirmPassword">

                    <button type="button"
                            class="toggle-password btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 text-muted">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                </div>

            </div>

            {{-- BOTÓN --}}
            <div class="col-12 text-end">

                <button type="submit"
                        class="btn btn-primary radius-lg text-normal px-4">
                    Actualizar contraseña
                </button>

            </div>

        </form>

    </div>

</div>
