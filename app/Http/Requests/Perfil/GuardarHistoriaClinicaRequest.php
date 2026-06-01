<?php

namespace App\Http\Requests\Perfil;

use Illuminate\Foundation\Http\FormRequest;

class GuardarHistoriaClinicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->id_pasaporte !== null;
    }

    public function rules(): array
    {
        return [
            // Perfil social y hábitos
            'ocupacion' => ['nullable', 'string', 'max:100'],
            'estado_civil' => ['nullable', 'string', 'in:Soltero,Casado,Divorciado,Viudo,Unión libre'],
            'escolaridad' => ['nullable', 'string', 'in:Sin estudios,Primaria,Secundaria,Bachillerato,Licenciatura,Posgrado'],
            'actividad_fisica' => ['nullable', 'string', 'max:60'],
            'tabaquismo' => ['nullable', 'string', 'in:No,Sí,Ex-fumador'],
            'alcohol' => ['nullable', 'string', 'in:No,Ocasional,Frecuente'],
            'alimentacion' => ['nullable', 'string', 'in:Balanceada,Desequilibrada,Vegetariana,Vegana'],

            // Antecedentes médicos
            'heredo_familiares' => ['nullable', 'array'],
            'heredo_familiares.*' => ['string', 'in:Diabetes,Hipertensión,Cáncer,Enfermedades cardíacas,Obesidad,Ninguno'],
            'personales_patologicos' => ['nullable', 'string', 'max:255'],
            'personales_no_patologicos' => ['nullable', 'string', 'max:255'],
            'enfermedades_previas' => ['nullable', 'string', 'max:255'],

            // Alertas de seguridad
            'alergias' => ['nullable', 'string', 'max:1000'],
            'cirugias' => ['nullable', 'string', 'max:1000'],
            'medicamentos' => ['nullable', 'string', 'max:1000'],
            'intolerancias' => ['nullable', 'string', 'max:1000'],

            // Estado general
            'peso' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'altura' => ['nullable', 'numeric', 'min:0.5', 'max:2.5'],
        ];
    }

    public function messages(): array
    {
        return [
            'peso.min' => 'El peso debe ser al menos 20 kg.',
            'peso.max' => 'El peso no puede superar 300 kg.',
            'altura.min' => 'La altura debe ser al menos 0.50 m.',
            'altura.max' => 'La altura no puede superar 2.50 m.',
        ];
    }
}
