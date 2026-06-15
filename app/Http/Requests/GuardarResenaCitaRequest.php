<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarResenaCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('pasaporte')->check();
    }

    public function rules(): array
    {
        return [
            'id_agenda'      => ['required', 'integer'],
            'accion'         => ['required', Rule::in(['enviar', 'rechazar'])],
            'review_value'   => ['nullable', 'integer', 'between:1,5'],
            'review_comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'accion.in'            => 'Acción no válida.',
            'review_value.between' => 'La calificación debe ser entre 1 y 5 estrellas.',
            'review_comment.max'   => 'El comentario no puede superar los 1000 caracteres.',
        ];
    }
}
