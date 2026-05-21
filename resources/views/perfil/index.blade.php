@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
    <div class="container">

        {{-- HEADER --}}
        <div class="row mb-4">
            <div class="col">
                <h3 class="fw-bold">Mi perfil</h3>
                <p class="text-muted">Información personal y clínica del paciente</p>
            </div>
        </div>

        {{-- BLOQUE 1: IDENTIDAD / PASAPORTE --}}
        @include('perfil.partials.identidad')

        {{-- BLOQUE 2: DATOS EDITABLES --}}
        @include('perfil.partials.datos-usuario')

        {{-- BLOQUE 3: SEGURIDAD --}}
        @include('perfil.partials.seguridad')

        {{-- BLOQUE 4: HISTORIA CLÍNICA --}}
        @include('perfil.partials.historia-clinica')


        <script>
            document.addEventListener("DOMContentLoaded", function() {

                document.querySelectorAll(".editable-field").forEach(field => {

                    const editBtn = field.querySelector(".edit-btn");
                    const cancelBtn = field.querySelector(".cancel-btn");
                    const saveBtn = field.querySelector(".save-btn");
                    const viewMode = field.querySelector(".view-mode");
                    const editMode = field.querySelector(".edit-mode");
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

                        const newValue = input.value;

                        // Actualiza visualmente
                        viewMode.querySelector("span").textContent = newValue;

                        editMode.classList.add("d-none");
                        viewMode.classList.remove("d-none");

                        // Aquí puedes enviar AJAX a Laravel
                        /*
                        fetch('/perfil/actualizar', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                campo: field.dataset.field,
                                valor: newValue
                            })
                        });
                        */

                    });

                });

            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {

                // Toggle mostrar contraseña
                document.querySelectorAll(".toggle-password").forEach(btn => {
                    btn.addEventListener("click", function() {
                        const input = this.parentElement.querySelector("input");
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

                // Indicador fuerza contraseña
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

                        const levels = ["Débil", "Regular", "Buena", "Fuerte"];
                        const colors = ["bg-danger", "bg-warning", "bg-info", "bg-success"];

                        strengthBar.className = "progress-bar";
                        strengthBar.classList.add(colors[strength - 1] || "bg-danger");
                        strengthBar.style.width = (strength * 25) + "%";

                        strengthText.textContent = levels[strength - 1] || "Muy débil";
                    });
                }

            });
        </script>


    </div>
@endsection
