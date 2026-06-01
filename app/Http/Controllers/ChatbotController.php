<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    public function ask(Request $request): JsonResponse
    {
        $question = trim((string) $request->input('question', ''));

        if ($question === '') {
            return response()->json(['ok' => false, 'error' => 'Escribe una pregunta para el asistente PATS.'], 422);
        }

        if (mb_strlen($question, 'UTF-8') > 1200) {
            return response()->json(['ok' => false, 'error' => 'La pregunta es demasiado larga.'], 422);
        }

        try {
            return response()->json($this->chatbot->ask($question));
        } catch (\Throwable) {
            return response()->json(['ok' => false, 'error' => 'No fue posible procesar tu consulta. Intenta nuevamente.'], 500);
        }
    }

    public function feedback(Request $request): JsonResponse
    {
        $logId = (int) $request->input('log_id', 0);
        $valor = strtoupper(trim((string) $request->input('valor', '')));
        $comentario = trim((string) $request->input('comentario', ''));

        if ($logId <= 0) {
            return response()->json(['ok' => false, 'error' => 'ID de conversación inválido.'], 422);
        }

        if (! in_array($valor, ['UTIL', 'NO_UTIL', 'INCORRECTO', 'CONFUSO'], true)) {
            return response()->json(['ok' => false, 'error' => 'Valor de feedback inválido.'], 422);
        }

        try {
            $this->chatbot->feedback($logId, $valor, $comentario);

            return response()->json(['ok' => true, 'message' => 'Feedback registrado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
