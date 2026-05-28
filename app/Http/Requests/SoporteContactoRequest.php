<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SoporteContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'email', 'max:150'],
            'mensaje' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es requerido.',
            'correo.required' => 'El correo es requerido.',
            'correo.email' => 'Ingresa un correo válido.',
            'mensaje.required' => 'Describe cómo podemos ayudarte.',
        ];
    }
}
