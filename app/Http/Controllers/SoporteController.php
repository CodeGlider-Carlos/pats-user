<?php

namespace App\Http\Controllers;

use App\Http\Requests\SoporteContactoRequest;
use App\Mail\SoporteContactoMail;
use App\Models\PatsSoporteContacto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class SoporteController extends Controller
{
    public function contacto(SoporteContactoRequest $request): JsonResponse
    {
        $contacto = PatsSoporteContacto::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'mensaje' => $request->mensaje,
        ]);

        Mail::to('sebas-_-sebastian@hotmail.com')->send(new SoporteContactoMail($contacto));

        return response()->json([
            'success' => true,
            'message' => '¡Mensaje enviado! Nos pondremos en contacto contigo pronto.',
        ]);
    }
}
