<div class="card mb-4 radius-lg shadow-sm">

    <div class="card-header radius-lg text-subtitle">
        Datos de usuario
    </div>

    <div class="card-body">

        <div class="row g-4">

            {{-- CAMPO --}}
            @php
                $campos = [
                    'nombre' => 'Juan',
                    'apellido_paterno' => 'Hernández',
                    'apellido_materno' => 'López',
                    'telefono' => '55 1234 5678',
                    'correo' => 'juan@email.com',
                    'rfc' => 'HELJ850812XXX',
                ];
            @endphp

            @foreach($campos as $key => $value)
            <div class="col-md-4">

                <div class="editable-field" data-field="{{ $key }}">

                    <div class="passport-label">
                        {{ ucfirst(str_replace('_',' ',$key)) }}
                    </div>

                    {{-- VIEW MODE --}}
                    <div class="view-mode text-normal d-flex justify-content-between align-items-center">
                        <span>{{ $value }}</span>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary radius-lg text-small edit-btn">
                            Editar
                        </button>
                    </div>

                    {{-- EDIT MODE --}}
                    <div class="edit-mode d-none mt-2">

                        <input type="text"
                               class="form-control radius-lg text-normal mb-2"
                               value="{{ $value }}">

                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary radius-lg text-small cancel-btn">
                                Cancelar
                            </button>

                            <button type="button"
                                    class="btn btn-sm btn-primary radius-lg text-small save-btn">
                                Guardar
                            </button>
                        </div>

                    </div>

                </div>

            </div>
            @endforeach

        </div>

    </div>

</div>
