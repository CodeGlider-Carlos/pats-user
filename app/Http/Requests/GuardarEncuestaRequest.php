<?php

namespace App\Http\Requests;

use App\Support\EncuestaPats;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarEncuestaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('pasaporte')->check();
    }

    public function rules(): array
    {
        $rules = [
            'id_tarjeta' => ['required', 'integer'],
            'accion' => ['required', Rule::in(['enviar', 'rechazar'])],
        ];

        foreach (EncuestaPats::preguntas() as $pregunta) {
            $key = $pregunta['key'];

            $rules[$key] = match ($pregunta['tipo']) {
                'escala5' => ['nullable', 'integer', 'between:1,5'],
                'escala10' => ['nullable', 'integer', 'between:0,10'],
                default => ['nullable', 'string', 'max:2000'],
            };

            if ($pregunta['comentario']) {
                $rules[$key.'_com'] = ['nullable', 'string', 'max:1000'];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'accion.in' => 'Acción no válida.',
            '*.between' => 'La calificación está fuera de rango.',
        ];
    }
}
