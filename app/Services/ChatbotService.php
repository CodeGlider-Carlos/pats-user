<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ChatbotService
{
    private function norm(string $txt): string
    {
        $txt = trim($txt);
        $txt = html_entity_decode($txt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $txt = mb_strtolower($txt, 'UTF-8');

        $map = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n', 'ç' => 'c',
        ];

        $txt = strtr($txt, $map);
        $txt = str_replace(['+', '#', '$', '%'], [' ', ' ', ' ', ' '], $txt);
        $txt = preg_replace('/[^a-z0-9\s]/u', ' ', $txt) ?? $txt;
        $txt = preg_replace('/\s+/u', ' ', $txt) ?? $txt;

        return trim($txt);
    }

    private function words(string $txt): array
    {
        $txt = $this->norm($txt);
        if ($txt === '') {
            return [];
        }

        $stop = [
            'que', 'q', 'k', 'como', 'cuando', 'donde', 'porque', 'por', 'para', 'con', 'sin',
            'del', 'de', 'la', 'el', 'los', 'las', 'un', 'una', 'unos', 'unas', 'y', 'o', 'a',
            'en', 'mi', 'me', 'su', 'sus', 'se', 'es', 'son', 'al', 'lo', 'le', 'les', 'te',
            'quiero', 'necesito', 'puedo', 'debo', 'tengo', 'hay', 'hacer', 'saber',
            'usuario', 'paciente', 'persona', 'dice', 'pregunta', 'pregunto', 'preguntan',
            'duda', 'dudas', 'apoyo', 'ayuda', 'ayudar', 'informacion', 'info',
            'pats', 'pasaporte', 'salud',
        ];

        $parts = preg_split('/\s+/u', $txt) ?: [];
        $out = [];

        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '' || mb_strlen($p, 'UTF-8') < 2 || in_array($p, $stop, true)) {
                continue;
            }
            $out[] = $p;
        }

        return array_values(array_unique($out));
    }

    private function isQueEsPats(string $question): bool
    {
        $q = $this->norm($question);

        $exact = ['pats', 'que es pats', 'q es pats', 'k es pats', 'que significa pats', 'que es pasaporte a tu salud', 'pasaporte a tu salud'];
        if (in_array($q, $exact, true)) {
            return true;
        }

        return (bool) (
            preg_match('/\b(que|q|k)\s+es\s+(el\s+)?(pats|pasaporte)\b/u', $q) ||
            preg_match('/\b(que|q|k)\s+significa\s+(pats|pasaporte)\b/u', $q) ||
            preg_match('/\bpara\s+que\s+sirve\s+(pats|pasaporte)\b/u', $q)
        );
    }

    private function contains(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        return mb_strpos($haystack, $needle, 0, 'UTF-8') !== false;
    }

    private function tableExists(string $table): bool
    {
        try {
            return count(DB::select('SHOW TABLES LIKE ?', [$table])) > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private function applySynonyms(string $question): string
    {
        if (! $this->tableExists('pats_bot_synonyms')) {
            return $question;
        }

        try {
            $rows = DB::table('pats_bot_synonyms')
                ->where('activo', 1)
                ->orderByRaw('LENGTH(termino_usuario) DESC')
                ->limit(900)
                ->get(['termino_usuario', 'termino_normalizado']);

            $expandedNorm = $this->norm($question);

            foreach ($rows as $row) {
                $from = trim($row->termino_usuario ?? '');
                $to = trim($row->termino_normalizado ?? '');

                if ($from === '' || $to === '') {
                    continue;
                }

                if ($this->contains($expandedNorm, $this->norm($from))) {
                    $question .= ' '.$to;
                }
            }
        } catch (\Throwable) {
            // Non-fatal
        }

        return trim($question);
    }

    private function detectIntent(string $question, string $originalQuestion = ''): ?string
    {
        if ($originalQuestion !== '' && $this->isQueEsPats($originalQuestion)) {
            return 'que_es_pats';
        }

        $q = $this->norm($question);
        if ($q === '') {
            return null;
        }

        $boostRules = [
            'pago' => [
                '/\b(donde|como|medios|formas|opciones)\b.*\b(pago|pagar|paga|pagos)\b/u',
                '/\b(pago|pagar|paga|pagos)\b.*\b(donde|como|medios|formas|opciones|caja|transferencia|link)\b/u',
                '/\b(medios de pago|formas de pago|opciones de pago|pagar en caja|donde se paga)\b/u',
            ],
            'costo' => [
                '/\b(costo|cuesta|vale|precio|mensualidad|anualidad|800|9600)\b/u',
            ],
            'vigencia' => [
                '/\b(vigencia|vigente|activo|activa|estatus|fecha de corte)\b/u',
            ],
            'falta_pago' => [
                '/\b(vencido|vencio|venido|inactivo|adeudo|atrasado|suspendido)\b/u',
                '/\b(no vigente|dejo de pagar|deje de pagar|no pago|debe meses)\b/u',
            ],
            'reactivacion' => [
                '/\b(reactivar|reactivacion|activar|volver a usar|volver a activar)\b/u',
            ],
            'cotizacion' => [
                '/\b(cotiza|cotizar|cotizacion|presupuesto|lead|cirugia|procedimiento|quirofano)\b/u',
            ],
            'precios_servicios' => [
                '/\b(precio servicio|precio estudio|precio consulta|precio especialista|tabulador|pats money|money)\b/u',
            ],
            'beneficios' => [
                '/\b(beneficios|incluye|que incluye|servicios incluidos|gratis|sin costo|descuento|precio preferencial)\b/u',
            ],
            'no_seguro' => [
                '/\b(seguro|poliza|cobertura|cubre|me cubre|le cubre|aseguradora)\b/u',
            ],
            'uso_qr' => [
                '/\b(qr|codigo qr|pasaporte digital|tarjeta digital|identificacion|ine)\b/u',
            ],
            'consulta_general' => [
                '/\b(consulta general|medicina general|doctor general)\b/u',
            ],
            'urgencias' => [
                '/\b(urgencias|emergencia|consulta urgencias|procedimiento urgencias)\b/u',
            ],
            'especialistas' => [
                '/\b(especialista|especialidad|medico especialista|consulta especialidad|cita|agenda)\b/u',
            ],
            'laboratorio' => [
                '/\b(laboratorio|lab|analisis|biometria|quimica sanguinea|estudio clinico)\b/u',
            ],
            'imagenologia' => [
                '/\b(imagen|imagenologia|rayos|rayos x|ultrasonido|placa|tomografia|resonancia)\b/u',
            ],
            'farmacia' => [
                '/\b(farmacia|medicamento|medicamentos|medicinas|receta)\b/u',
            ],
            'hospitalizacion' => [
                '/\b(hospitalizacion|hospital|internamiento|servicios hospitalarios)\b/u',
            ],
            'cirugia' => [
                '/\b(cirugia|quirofano|operacion|procedimiento quirurgico|honorarios)\b/u',
            ],
            'uso_ilimitado' => [
                '/\b(uso ilimitado|cuantas veces|limite|preexistencias)\b/u',
            ],
            'exclusiones' => [
                '/\b(no incluye|exclusion|exclusiones|no cubre|trasplante|oncologico|hemodialisis|experimental)\b/u',
            ],
            'cancelacion' => [
                '/\b(cancelar|cancelacion|baja|terminar contrato|no renovar|reembolso|devolucion)\b/u',
            ],
            'privacidad' => [
                '/\b(datos personales|privacidad|arco|cifrado|proteccion de datos|lfpdppp)\b/u',
            ],
            'canalizar' => [
                '/\b(concierge|admision|soporte|ayuda|asesor|personal autorizado|quien ayuda)\b/u',
            ],
        ];

        $scores = [];

        foreach ($boostRules as $intent => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $q)) {
                    $scores[$intent] = ($scores[$intent] ?? 0) + 20;
                }
            }
        }

        if (($scores['falta_pago'] ?? 0) > 0) {
            unset($scores['vigencia'], $scores['pago']);
        }
        if (($scores['reactivacion'] ?? 0) > 0) {
            unset($scores['vigencia']);
        }
        if (($scores['cotizacion'] ?? 0) > 0) {
            unset($scores['costo'], $scores['precios_servicios']);
        }

        try {
            if ($this->tableExists('pats_bot_intents')) {
                $rows = DB::table('pats_bot_intents')
                    ->where('activo', 1)
                    ->orderBy('id')
                    ->get(['intent', 'keywords']);

                $skip = ['pats', 'pasaporte', 'pasaporte a tu salud', 'beneficios', 'descuentos', 'servicios'];

                foreach ($rows as $row) {
                    $intent = trim($row->intent ?? '');
                    $keywordsRaw = trim($row->keywords ?? '');

                    if ($intent === '' || $keywordsRaw === '') {
                        continue;
                    }

                    $keywords = preg_split('/[,|]+/u', $keywordsRaw) ?: [];
                    $score = 0;

                    foreach ($keywords as $kw) {
                        $kw = $this->norm($kw);
                        if ($kw === '' || in_array($kw, $skip, true)) {
                            continue;
                        }

                        if ($q === $kw) {
                            $score += 12;
                        } elseif (mb_strlen($kw, 'UTF-8') >= 4 && $this->contains($q, $kw)) {
                            $score += 7;
                        } else {
                            foreach (preg_split('/\s+/u', $kw) ?: [] as $p) {
                                if (mb_strlen($p, 'UTF-8') >= 4 && $this->contains($q, $p)) {
                                    $score++;
                                }
                            }
                        }
                    }

                    if ($score > 0) {
                        $scores[$intent] = ($scores[$intent] ?? 0) + $score;
                    }
                }
            }
        } catch (\Throwable) {
            // Non-fatal
        }

        if (! $scores) {
            return null;
        }

        arsort($scores);
        $bestIntent = (string) array_key_first($scores);
        $bestScore = (int) ($scores[$bestIntent] ?? 0);

        return $bestScore >= 6 ? $bestIntent : null;
    }

    private function scoreText(string $query, array $tokens, string $haystack, int $priority = 10, ?string $detectedIntent = null, ?string $rowIntent = null): float
    {
        $q = $this->norm($query);
        $h = $this->norm($haystack);

        if ($q === '' || $h === '') {
            return 0.0;
        }

        $score = 0.0;

        if ($detectedIntent !== null && $rowIntent !== null && $detectedIntent === $rowIntent) {
            $score += 0.42;
        }

        if ($this->contains($h, $q)) {
            $score += 0.38;
        }

        if ($tokens) {
            $hits = 0;
            foreach ($tokens as $t) {
                if ($t !== '' && $this->contains($h, $t)) {
                    $hits++;
                }
            }
            $ratio = $hits / max(1, count($tokens));
            $score += $ratio * 0.48;
            if ($hits === count($tokens)) {
                $score += 0.10;
            }
        }

        $score += min(0.10, max(0, $priority) / 10000);

        return min(1.0, $score);
    }

    private function searchManual(string $question, string $originalQuestion = ''): ?array
    {
        $qNorm = $this->norm($question);
        $tokens = $this->words($question);
        $shortcut = $originalQuestion !== '' ? $originalQuestion : $question;

        if ($this->isQueEsPats($shortcut)) {
            try {
                $row = DB::table('pats_bot_knowledge')
                    ->where('activo', 1)
                    ->where('intent', 'que_es_pats')
                    ->orderByDesc('prioridad')
                    ->orderBy('id')
                    ->first(['id', 'categoria', 'subcategoria', 'intent', 'pregunta_base', 'respuesta', 'respuesta_corta', 'fuente', 'prioridad']);

                if ($row) {
                    return array_merge((array) $row, [
                        'source_type' => 'MANUAL',
                        'search_method' => 'SHORTCUT_QUE_ES_PATS',
                        'score' => 1.0,
                        'detected_intent' => 'que_es_pats',
                    ]);
                }
            } catch (\Throwable) {
            }
        }

        $detectedIntent = $this->detectIntent($question, $originalQuestion);

        if (! $tokens && $detectedIntent === null) {
            return null;
        }

        try {
            $query = DB::table('pats_bot_knowledge')
                ->where('activo', 1)
                ->select(['id', 'categoria', 'subcategoria', 'intent', 'pregunta_base', 'respuesta', 'respuesta_corta', 'keywords', 'fuente', 'prioridad']);

            if ($detectedIntent !== null) {
                $query->where('intent', $detectedIntent);
            }

            $rows = $query
                ->orderByDesc('prioridad')
                ->orderBy('id')
                ->limit($detectedIntent !== null ? 500 : 1000)
                ->get();

            $best = null;
            $bestScore = 0.0;

            foreach ($rows as $row) {
                $rowIntent = trim($row->intent ?? '');

                if ($detectedIntent !== null && $rowIntent !== $detectedIntent) {
                    continue;
                }

                $haystack = implode(' ', [
                    $row->categoria ?? '',
                    $row->subcategoria ?? '',
                    $row->intent ?? '',
                    $row->pregunta_base ?? '',
                    $row->respuesta_corta ?? '',
                    $row->keywords ?? '',
                    $row->respuesta ?? '',
                ]);

                $score = $this->scoreText($qNorm, $tokens, $haystack, (int) ($row->prioridad ?? 10), $detectedIntent, $rowIntent);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = (array) $row;
                }
            }

            if ($best !== null && $bestScore >= 0.22) {
                unset($best['keywords']);

                return array_merge($best, [
                    'source_type' => 'MANUAL',
                    'search_method' => $detectedIntent !== null ? 'INTENT_SCORE' : 'PHP_SCORE',
                    'score' => round($bestScore, 4),
                    'detected_intent' => $detectedIntent,
                ]);
            }
        } catch (\Throwable) {
        }

        // Fulltext fallback
        try {
            $row = DB::table('pats_bot_knowledge')
                ->where('activo', 1)
                ->selectRaw('id, categoria, subcategoria, intent, pregunta_base, respuesta, respuesta_corta, fuente, prioridad, MATCH(pregunta_base, respuesta, keywords) AGAINST (? IN NATURAL LANGUAGE MODE) AS score', [$qNorm])
                ->whereRaw('MATCH(pregunta_base, respuesta, keywords) AGAINST (? IN NATURAL LANGUAGE MODE)', [$qNorm])
                ->orderByDesc('score')
                ->orderByDesc('prioridad')
                ->first();

            if ($row) {
                return array_merge((array) $row, [
                    'source_type' => 'MANUAL',
                    'search_method' => 'FULLTEXT',
                    'score' => min(0.55, (float) ($row->score ?? 0)),
                    'detected_intent' => $detectedIntent,
                ]);
            }
        } catch (\Throwable) {
        }

        return null;
    }

    private function detectFlags(string $question): array
    {
        $q = $this->norm($question);

        $checks = [
            'precio' => ['precio', 'cuesta', 'costo', 'descuento', 'tabulador', 'cobro', 'cobraron', 'pagar', 'money'],
            'reactivacion' => ['reactivar', 'reactivacion', 'vencido', 'vencida', 'suspendido', 'suspendida', 'vigencia', 'vigente', 'comprobante', 'voucher'],
            'cotizacion' => ['cotizar', 'cotizacion', 'cirugia', 'procedimiento', 'quirofano', 'paquete', 'lead'],
            'queja' => ['queja', 'inconformidad', 'molesto', 'mal', 'no respetaron', 'cobraron mal', 'reclamo'],
            'no_seguro' => ['seguro', 'poliza', 'cubre', 'cobertura'],
            'datos' => ['id pats', 'nombre', 'telefono', 'correo', 'comprobante', 'referencia', 'datos'],
        ];

        $flags = [];
        foreach ($checks as $flag => $terms) {
            foreach ($terms as $term) {
                if ($this->contains($q, $this->norm($term))) {
                    $flags[] = $flag;
                    break;
                }
            }
        }

        return array_values(array_unique($flags));
    }

    private function logQuery(string $question, ?string $answer, ?string $intent, ?int $knowledgeId, ?float $score, string $source): int
    {
        try {
            if (! $this->tableExists('pats_bot_logs')) {
                return 0;
            }

            return (int) DB::table('pats_bot_logs')->insertGetId([
                'usuario' => 'web_publico',
                'rol' => 'USUARIO_WEB',
                'region' => '',
                'unidad' => '',
                'pregunta' => $question,
                'respuesta' => $answer,
                'intent_detectado' => $intent,
                'knowledge_id' => $knowledgeId,
                'score' => $score,
                'origen_respuesta' => $source,
                'ip' => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function queueTraining(int $logId, string $pregunta, ?string $respuesta, string $motivo, ?string $intent, ?string $source, ?float $score): void
    {
        $allowed = ['SIN_RESULTADO', 'BAJA_CONFIANZA', 'INCORRECTO', 'CONFUSO', 'NO_UTIL', 'REPETIDA'];
        if (! in_array($motivo, $allowed, true) || ! $this->tableExists('pats_bot_training_queue')) {
            return;
        }

        try {
            $pregNorm = $this->norm($pregunta);

            $existing = DB::table('pats_bot_training_queue')
                ->where('estado', 'PENDIENTE')
                ->where('motivo', $motivo)
                ->whereRaw('LOWER(TRIM(pregunta)) = LOWER(TRIM(?))', [$pregNorm])
                ->first(['id']);

            if ($existing) {
                DB::table('pats_bot_training_queue')
                    ->where('id', $existing->id)
                    ->update([
                        'veces_detectada' => DB::raw('veces_detectada + 1'),
                        'updated_at' => now(),
                    ]);

                return;
            }

            DB::table('pats_bot_training_queue')->insert([
                'log_id' => $logId ?: null,
                'pregunta' => $pregNorm,
                'respuesta_actual' => $respuesta,
                'motivo' => $motivo,
                'intent_detectado' => $intent,
                'source' => $source,
                'score' => $score,
                'veces_detectada' => 1,
                'estado' => 'PENDIENTE',
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // Non-fatal
        }
    }

    public function ask(string $question): array
    {
        $questionOriginal = $question;
        $questionExpanded = $this->applySynonyms($questionOriginal);
        $detectedIntent = $this->detectIntent($questionExpanded, $questionOriginal);
        $flags = $this->detectFlags($questionExpanded);

        $manual = $this->searchManual($questionExpanded, $questionOriginal);

        if ($manual === null) {
            $fallback = 'No encontré información sobre eso. Puedes contactar a soporte desde la sección "Soporte" en el menú.';
            $logId = $this->logQuery($questionOriginal, $fallback, $detectedIntent, null, 0.0, 'SIN_RESULTADO');
            $this->queueTraining($logId, $questionOriginal, $fallback, 'SIN_RESULTADO', $detectedIntent, 'SIN_RESULTADO', 0.0);

            return [
                'ok' => true,
                'answer' => $fallback,
                'respuesta_usuario_sugerida' => $fallback,
                'intent' => $detectedIntent,
                'source' => 'SIN_RESULTADO',
                'confidence' => 0.0,
                'flags' => $flags,
                'log_id' => $logId,
            ];
        }

        $respuesta = trim($manual['respuesta'] ?? '');
        $intent = $manual['intent'] ?? $detectedIntent;
        $knowledgeId = (int) ($manual['id'] ?? 0);
        $confidence = min(1.0, (float) ($manual['score'] ?? 0.0));

        $logId = $this->logQuery($questionOriginal, $respuesta, $intent, $knowledgeId ?: null, $confidence, 'MANUAL_PATS');

        if ($confidence > 0 && $confidence < 0.55) {
            $this->queueTraining($logId, $questionOriginal, $respuesta, 'BAJA_CONFIANZA', $intent, 'MANUAL_PATS', $confidence);
        }

        return [
            'ok' => true,
            'answer' => $respuesta,
            'respuesta_usuario_sugerida' => $respuesta,
            'intent' => $intent,
            'source' => 'MANUAL_PATS',
            'confidence' => round($confidence, 4),
            'flags' => $flags,
            'log_id' => $logId,
        ];
    }

    public function feedback(int $logId, string $valor, string $comentario = ''): void
    {
        $allowed = ['UTIL', 'NO_UTIL', 'INCORRECTO', 'CONFUSO'];
        if (! in_array($valor, $allowed, true)) {
            throw new \InvalidArgumentException('Valor de feedback inválido.');
        }

        if (! $this->tableExists('pats_bot_feedback') || ! $this->tableExists('pats_bot_logs')) {
            throw new \RuntimeException('El sistema de feedback no está disponible.');
        }

        $log = DB::table('pats_bot_logs')->where('id', $logId)->first(['id']);
        if (! $log) {
            throw new \RuntimeException('No se encontró el registro de conversación.');
        }

        DB::table('pats_bot_feedback')->insert([
            'log_id' => $logId,
            'usuario' => 'web_publico',
            'valor' => $valor,
            'comentario' => $comentario !== '' ? $comentario : null,
            'created_at' => now(),
        ]);

        if (in_array($valor, ['INCORRECTO', 'CONFUSO', 'NO_UTIL'], true)) {
            $this->queueTraining($logId, '', null, $valor, null, null, null);
        }
    }
}
