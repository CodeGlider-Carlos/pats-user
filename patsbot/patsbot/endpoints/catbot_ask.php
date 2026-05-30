<?php
/*
=========================================================
ez/patsbot/endpoints/catbot_ask.php
CAT BOT PATS
Manual de Usuario / Asistente Operativo / Entrenamiento Supervisado

BD: PATS mediante $db_pats
Bootstrap: catbot_bootstrap.php
=========================================================
*/

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  @session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/catbot_bootstrap.php';

/* =========================================================
   JSON
========================================================= */

function catbot_json(array $data, int $code = 200): void {
  http_response_code($code);
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function catbot_fail(string $msg, int $code = 422, array $extra = []): void {
  catbot_json(array_merge([
    'ok' => false,
    'error' => $msg
  ], $extra), $code);
}

/* =========================================================
   VALIDAR PDO PATS
========================================================= */

if (!isset($db_pats) || !($db_pats instanceof PDO)) {
  catbot_fail('No se encontró la conexión PDO $db_pats para la base PATS.', 500);
}

$db = $db_pats;

try {
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $db->exec("SET NAMES utf8mb4");
} catch (Throwable $e) {
  catbot_fail('No fue posible preparar la conexión del CAT BOT PATS.', 500);
}

/* =========================================================
   SESIÓN / CONTEXTO
========================================================= */

$rol      = strtoupper(trim((string)($CATBOT_ROLE ?? ($_SESSION['rol'] ?? ''))));
$userLog  = trim((string)($CATBOT_USER ?? ($_SESSION['usuario'] ?? '')));
$userName = trim((string)($CATBOT_NAME ?? ($_SESSION['nombre'] ?? $userLog)));
$region   = trim((string)($CATBOT_REGION ?? ($_SESSION['acroregion'] ?? ($_SESSION['region'] ?? ''))));
$unidad   = trim((string)($CATBOT_UNIDAD ?? ($_SESSION['acronu'] ?? ($_SESSION['unidad'] ?? ''))));

$rolesPermitidos = [
  'ADMIN',
  'ADMINPATS',
  'CON',
  'CONCIERGE',
  'ADM',
  'ADMISION',
  'CAJ',
  'CAJA',
  'RECEPCION'
];

if (!in_array($rol, $rolesPermitidos, true)) {
  catbot_fail('El Asistente PATS está disponible solo para Concierge, Admisión, Caja, ADMIN o ADMINPATS.', 403, [
    'rol' => $rol
  ]);
}

/* =========================================================
   HELPERS DE TEXTO
========================================================= */

function catbot_contains(string $haystack, string $needle): bool {
  if ($needle === '') return true;
  return mb_strpos($haystack, $needle, 0, 'UTF-8') !== false;
}

function catbot_norm(string $txt): string {
  $txt = trim($txt);
  $txt = html_entity_decode($txt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $txt = mb_strtolower($txt, 'UTF-8');

  $map = [
    'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a',
    'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
    'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
    'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
    'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
    'ñ' => 'n',
    'ç' => 'c'
  ];

  $txt = strtr($txt, $map);
  $txt = str_replace(['+', '#', '$', '%'], [' ', ' ', ' ', ' '], $txt);
  $txt = preg_replace('/[^a-z0-9\s]/u', ' ', $txt);
  $txt = preg_replace('/\s+/u', ' ', (string)$txt);

  return trim((string)$txt);
}

function catbot_words(string $txt): array {
  $txt = catbot_norm($txt);
  if ($txt === '') return [];

  $parts = preg_split('/\s+/u', $txt) ?: [];

  /*
    IMPORTANTÍSIMO:
    PATS / pasaporte / salud son contexto.
    No deben decidir la intención, porque si no todo cae en "qué es PATS".
  */
  $stop = [
    'que','q','k','como','cuando','donde','porque','por','para','con','sin',
    'del','de','la','el','los','las','un','una','unos','unas','y','o','a',
    'en','mi','me','su','sus','se','es','son','al','lo','le','les','te',
    'quiero','necesito','puedo','debo','tengo','hay','hacer','saber',
    'usuario','paciente','persona','dice','pregunta','pregunto','preguntan',
    'duda','dudas','apoyo','ayuda','ayudar','informacion','info',
    'pats','pasaporte','salud'
  ];

  $out = [];

  foreach ($parts as $p) {
    $p = trim($p);
    if ($p === '') continue;
    if (mb_strlen($p, 'UTF-8') < 2) continue;
    if (in_array($p, $stop, true)) continue;
    $out[] = $p;
  }

  return array_values(array_unique($out));
}

function catbot_short(string $txt, int $max = 900): string {
  $txt = trim($txt);
  if ($txt === '') return '';
  if (mb_strlen($txt, 'UTF-8') <= $max) return $txt;
  return rtrim(mb_substr($txt, 0, $max, 'UTF-8')) . '...';
}

function catbot_rows_exist(PDO $db, string $table): bool {
  try {
    $stmt = $db->query("SHOW TABLES LIKE " . $db->quote($table));
    return (bool)$stmt->fetchColumn();
  } catch (Throwable $e) {
    return false;
  }
}

/* =========================================================
   DETECTOR REAL DE "QUÉ ES PATS"
========================================================= */

function catbot_is_que_es_pats(string $question): bool {
  $q = catbot_norm($question);

  $exact = [
    'pats',
    'que es pats',
    'q es pats',
    'k es pats',
    'que significa pats',
    'que es pasaporte a tu salud',
    'pasaporte a tu salud'
  ];

  if (in_array($q, $exact, true)) {
    return true;
  }

  /*
    Solo dispara definición si realmente pregunta definición.
    NO debe dispararse para:
    - donde se paga pats
    - cuanto cuesta pats
    - pats vencido
    - quien cotiza pats
  */
  if (
    preg_match('/\b(que|q|k)\s+es\s+(el\s+)?(pats|pasaporte)\b/u', $q) ||
    preg_match('/\b(que|q|k)\s+significa\s+(pats|pasaporte)\b/u', $q) ||
    preg_match('/\bpara\s+que\s+sirve\s+(pats|pasaporte)\b/u', $q)
  ) {
    return true;
  }

  return false;
}

/* =========================================================
   SINÓNIMOS
========================================================= */

function catbot_apply_synonyms(PDO $db, string $question): string {
  $expanded = $question;

  if (!catbot_rows_exist($db, 'pats_bot_synonyms')) {
    return $expanded;
  }

  try {
    $stmt = $db->prepare("
      SELECT termino_usuario, termino_normalizado
      FROM pats_bot_synonyms
      WHERE activo = 1
      ORDER BY LENGTH(termino_usuario) DESC
      LIMIT 900
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $expandedNorm = catbot_norm($expanded);

    foreach ($rows as $row) {
      $from = trim((string)($row['termino_usuario'] ?? ''));
      $to   = trim((string)($row['termino_normalizado'] ?? ''));

      if ($from === '' || $to === '') continue;

      $fromNorm = catbot_norm($from);

      if ($fromNorm !== '' && catbot_contains($expandedNorm, $fromNorm)) {
        $expanded .= ' ' . $to;
      }
    }
  } catch (Throwable $e) {
    // No romper por sinónimos
  }

  return trim($expanded);
}

/* =========================================================
   DETECTOR DE INTENT DESDE BD + BOOSTS
========================================================= */

function catbot_detect_intent_from_db(PDO $db, string $question, string $originalQuestion = ''): ?string {
  $sourceQuestion = trim($question);
  $original = trim($originalQuestion);

  if ($original !== '' && catbot_is_que_es_pats($original)) {
    return 'que_es_pats';
  }

  $q = catbot_norm($sourceQuestion);
  if ($q === '') return null;

  /*
    Boosts fuertes. Esto corrige preguntas cortas:
    costo, donde se paga, quien cotiza, pasaporte venido, etc.
  */
  $boostRules = [
    'pago' => [
      '/\b(donde|como|medios|formas|opciones)\b.*\b(pago|pagar|paga|pagos)\b/u',
      '/\b(pago|pagar|paga|pagos)\b.*\b(donde|como|medios|formas|opciones|caja|transferencia|link)\b/u',
      '/\b(medios de pago|formas de pago|opciones de pago|pagar en caja|donde se paga)\b/u'
    ],

    'costo' => [
      '/\b(costo|cuesta|vale|precio|mensualidad|anualidad|800|9600)\b/u',
      '/\b(cuanto cuesta|cuanto vale|precio pats|costo pats)\b/u'
    ],

    'vigencia' => [
      '/\b(vigencia|vigente|activo|activa|estatus|fecha de corte)\b/u',
      '/\b(esta activo|esta vigente|donde veo vigencia|como veo vigencia)\b/u'
    ],

    'falta_pago' => [
      '/\b(vencido|vencio|venido|inactivo|adeudo|atrasado|suspendido)\b/u',
      '/\b(no vigente|dejo de pagar|deje de pagar|no pago|debe meses)\b/u'
    ],

    'reactivacion' => [
      '/\b(reactivar|reactivacion|activar|volver a usar|volver a activar)\b/u',
      '/\b(reactivar con comprobante|activar pasaporte)\b/u'
    ],

    'cotizacion' => [
      '/\b(cotiza|cotizar|cotizacion|presupuesto|lead|cirugia|procedimiento|quirofano)\b/u',
      '/\b(quien cotiza|quien hace cotizacion|precio cirugia)\b/u'
    ],

    'precios_servicios' => [
      '/\b(precio servicio|precio estudio|precio consulta|precio especialista|tabulador|pats money|money)\b/u'
    ],

    'beneficios' => [
      '/\b(beneficios|incluye|que incluye|servicios incluidos|gratis|sin costo|descuento|precio preferencial)\b/u'
    ],

    'no_seguro' => [
      '/\b(seguro|poliza|cobertura|cubre|me cubre|le cubre|aseguradora)\b/u'
    ],

    'uso_qr' => [
      '/\b(qr|codigo qr|pasaporte digital|tarjeta digital|identificacion|ine)\b/u',
      '/\b(no aparece qr|no tengo qr|sin qr|no sale qr)\b/u'
    ],

    'consulta_general' => [
      '/\b(consulta general|medicina general|doctor general)\b/u'
    ],

    'urgencias' => [
      '/\b(urgencias|emergencia|consulta urgencias|procedimiento urgencias)\b/u'
    ],

    'especialistas' => [
      '/\b(especialista|especialidad|medico especialista|consulta especialidad|cita|agenda)\b/u'
    ],

    'laboratorio' => [
      '/\b(laboratorio|lab|analisis|biometria|quimica sanguinea|estudio clinico)\b/u'
    ],

    'imagenologia' => [
      '/\b(imagen|imagenologia|rayos|rayos x|ultrasonido|placa|tomografia|resonancia)\b/u'
    ],

    'farmacia' => [
      '/\b(farmacia|medicamento|medicamentos|medicinas|receta)\b/u'
    ],

    'hospitalizacion' => [
      '/\b(hospitalizacion|hospital|internamiento|servicios hospitalarios)\b/u'
    ],

    'cirugia' => [
      '/\b(cirugia|quirofano|operacion|procedimiento quirurgico|honorarios)\b/u'
    ],

    'terapia_intensiva' => [
      '/\b(terapia intensiva|uci|uti|intensiva)\b/u'
    ],

    'uso_ilimitado' => [
      '/\b(uso ilimitado|cuantas veces|limite|preexistencias)\b/u'
    ],

    'exclusiones' => [
      '/\b(no incluye|exclusion|exclusiones|no cubre|trasplante|oncologico|hemodialisis|experimental)\b/u',
      '/\b(fuera de red|no afiliado|no autorizado|otro hospital|otro medico)\b/u'
    ],

    'fuera_red' => [
      '/\b(fuera de red|no afiliado|no autorizado|otro hospital|otro medico)\b/u'
    ],

    'mayores_65' => [
      '/\b(65|mayor de 65|adulto mayor|sesenta y cinco)\b/u'
    ],

    'menores_dependientes' => [
      '/\b(menor|menores|18 anos|dependiente|tutor|padre|madre)\b/u'
    ],

    'cancelacion' => [
      '/\b(cancelar|cancelacion|baja|terminar contrato|no renovar|reembolso|devolucion)\b/u'
    ],

    'responsabilidad_medica' => [
      '/\b(responsabilidad medica|negligencia|resultado medico|doctor|prestador)\b/u'
    ],

    'privacidad' => [
      '/\b(datos personales|privacidad|arco|cifrado|proteccion de datos|lfpdppp)\b/u'
    ],

    'recomendaciones' => [
      '/\b(recomendaciones|consejos|usar bien|pagar a tiempo|verificar red)\b/u'
    ],

    'canalizar' => [
      '/\b(concierge|admision|soporte|ayuda|asesor|personal autorizado|quien ayuda|a quien lo mando)\b/u'
    ]
  ];

  $manualScores = [];

  foreach ($boostRules as $intent => $patterns) {
    foreach ($patterns as $pattern) {
      if (preg_match($pattern, $q)) {
        $manualScores[$intent] = ($manualScores[$intent] ?? 0) + 20;
      }
    }
  }

  /*
    Reglas de prioridad entre intents parecidos.
  */
  if (($manualScores['falta_pago'] ?? 0) > 0) {
    unset($manualScores['vigencia']);
    unset($manualScores['pago']);
  }

  if (($manualScores['reactivacion'] ?? 0) > 0) {
    unset($manualScores['vigencia']);
  }

  if (($manualScores['cotizacion'] ?? 0) > 0) {
    unset($manualScores['costo']);
    unset($manualScores['precios_servicios']);
  }

  /*
    Ahora usa pats_bot_intents como router adicional.
  */
  try {
    if (catbot_rows_exist($db, 'pats_bot_intents')) {
      $stmt = $db->prepare("
        SELECT intent, keywords
        FROM pats_bot_intents
        WHERE activo = 1
        ORDER BY id ASC
      ");
      $stmt->execute();
      $rows = $stmt->fetchAll();

      foreach ($rows as $row) {
        $intent = trim((string)($row['intent'] ?? ''));
        $keywordsRaw = trim((string)($row['keywords'] ?? ''));

        if ($intent === '' || $keywordsRaw === '') continue;

        $keywords = preg_split('/[,|]+/u', $keywordsRaw) ?: [];
        $score = 0;

        foreach ($keywords as $kw) {
          $kw = catbot_norm($kw);
          if ($kw === '') continue;

          /*
            Estas son demasiado generales. No deben decidir.
          */
          if (in_array($kw, [
            'pats',
            'pasaporte',
            'pasaporte a tu salud',
            'beneficios',
            'descuentos',
            'servicios'
          ], true)) {
            continue;
          }

          if ($q === $kw) {
            $score += 12;
          } elseif (mb_strlen($kw, 'UTF-8') >= 4 && catbot_contains($q, $kw)) {
            $score += 7;
          } else {
            $parts = preg_split('/\s+/u', $kw) ?: [];
            foreach ($parts as $p) {
              if (mb_strlen($p, 'UTF-8') >= 4 && catbot_contains($q, $p)) {
                $score += 1;
              }
            }
          }
        }

        if ($score > 0) {
          $manualScores[$intent] = ($manualScores[$intent] ?? 0) + $score;
        }
      }
    }
  } catch (Throwable $e) {
    // Sin intents BD, seguimos con boosts manuales.
  }

  if (!$manualScores) {
    return null;
  }

  arsort($manualScores);
  $bestIntent = array_key_first($manualScores);
  $bestScore = (int)($manualScores[$bestIntent] ?? 0);

  return $bestScore >= 6 ? $bestIntent : null;
}

/* =========================================================
   LECTURA INPUT
========================================================= */

$raw = file_get_contents('php://input');
$input = [];

if ($raw !== false && trim($raw) !== '') {
  $decoded = json_decode($raw, true);
  if (is_array($decoded)) {
    $input = $decoded;
  }
}

$question = trim((string)(
  $input['question']
  ?? $input['pregunta']
  ?? $_POST['question']
  ?? $_POST['pregunta']
  ?? $_GET['question']
  ?? $_GET['pregunta']
  ?? ''
));

$mode = strtolower(trim((string)($input['mode'] ?? $_POST['mode'] ?? $_GET['mode'] ?? 'manual')));

if (!in_array($mode, ['manual', 'operativo', 'auto'], true)) {
  $mode = 'manual';
}

if ($question === '' && !isset($_GET['debug_count']) && !isset($_GET['debug_search'])) {
  catbot_fail('Escribe una pregunta para el asistente PATS.');
}

if (mb_strlen($question, 'UTF-8') > 1200) {
  catbot_fail('La pregunta es demasiado larga. Resume la duda para poder ayudarte mejor.');
}

/* =========================================================
   LOGS / ENTRENAMIENTO
========================================================= */

function catbot_log(
  PDO $db,
  string $question,
  ?string $answer,
  ?string $intent,
  ?int $knowledgeId,
  ?float $score,
  string $source,
  string $rol,
  string $userLog,
  string $region,
  string $unidad
): int {
  try {
    if (!catbot_rows_exist($db, 'pats_bot_logs')) return 0;

    $stmt = $db->prepare("
      INSERT INTO pats_bot_logs
      (usuario, rol, region, unidad, pregunta, respuesta, intent_detectado, knowledge_id, score, origen_respuesta, ip, user_agent)
      VALUES
      (:usuario, :rol, :region, :unidad, :pregunta, :respuesta, :intent, :kid, :score, :origen, :ip, :ua)
    ");

    $stmt->execute([
      ':usuario'   => $userLog,
      ':rol'       => $rol,
      ':region'    => $region,
      ':unidad'    => $unidad,
      ':pregunta'  => $question,
      ':respuesta' => $answer,
      ':intent'    => $intent,
      ':kid'       => $knowledgeId,
      ':score'     => $score,
      ':origen'    => $source,
      ':ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
      ':ua'        => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255)
    ]);

    return (int)$db->lastInsertId();
  } catch (Throwable $e) {
    return 0;
  }
}

function catbot_queue_training(
  PDO $db,
  ?int $logId,
  string $pregunta,
  ?string $respuestaActual,
  string $motivo,
  ?string $intent,
  ?string $source,
  ?float $score
): void {
  if (!catbot_rows_exist($db, 'pats_bot_training_queue')) {
    return;
  }

  $motivosPermitidos = [
    'SIN_RESULTADO',
    'BAJA_CONFIANZA',
    'INCORRECTO',
    'CONFUSO',
    'NO_UTIL',
    'REPETIDA'
  ];

  if (!in_array($motivo, $motivosPermitidos, true)) {
    return;
  }

  try {
    $pregNorm = catbot_norm($pregunta);

    $find = $db->prepare("
      SELECT id
      FROM pats_bot_training_queue
      WHERE estado = 'PENDIENTE'
        AND motivo = :motivo
        AND LOWER(TRIM(pregunta)) = LOWER(TRIM(:pregunta))
      LIMIT 1
    ");

    $find->execute([
      ':motivo' => $motivo,
      ':pregunta' => $pregNorm
    ]);

    $existing = $find->fetch();

    if ($existing) {
      $upd = $db->prepare("
        UPDATE pats_bot_training_queue
        SET
          veces_detectada = veces_detectada + 1,
          updated_at = NOW(),
          log_id = COALESCE(:log_id, log_id),
          respuesta_actual = COALESCE(:respuesta_actual, respuesta_actual),
          intent_detectado = COALESCE(:intent_detectado, intent_detectado),
          source = COALESCE(:source, source),
          score = COALESCE(:score, score)
        WHERE id = :id
        LIMIT 1
      ");

      $upd->execute([
        ':log_id' => $logId,
        ':respuesta_actual' => $respuestaActual,
        ':intent_detectado' => $intent,
        ':source' => $source,
        ':score' => $score,
        ':id' => (int)$existing['id']
      ]);

      return;
    }

    $ins = $db->prepare("
      INSERT INTO pats_bot_training_queue
      (log_id, pregunta, respuesta_actual, motivo, intent_detectado, source, score, veces_detectada, estado)
      VALUES
      (:log_id, :pregunta, :respuesta_actual, :motivo, :intent_detectado, :source, :score, 1, 'PENDIENTE')
    ");

    $ins->execute([
      ':log_id' => $logId,
      ':pregunta' => $pregNorm,
      ':respuesta_actual' => $respuestaActual,
      ':motivo' => $motivo,
      ':intent_detectado' => $intent,
      ':source' => $source,
      ':score' => $score
    ]);

  } catch (Throwable $e) {
    // No romper flujo
  }
}

/* =========================================================
   DEBUG COUNT
========================================================= */

if (isset($_GET['debug_count'])) {
  $manualCount = 0;
  $opsCount = 0;
  $synCount = 0;
  $trainCount = 0;

  try {
    $manualCount = (int)$db->query("SELECT COUNT(*) FROM pats_bot_knowledge WHERE activo = 1")->fetchColumn();
  } catch (Throwable $e) {}

  try {
    if (catbot_rows_exist($db, 'pats_bot_ops_knowledge')) {
      $opsCount = (int)$db->query("SELECT COUNT(*) FROM pats_bot_ops_knowledge WHERE activo = 1")->fetchColumn();
    }
  } catch (Throwable $e) {}

  try {
    if (catbot_rows_exist($db, 'pats_bot_synonyms')) {
      $synCount = (int)$db->query("SELECT COUNT(*) FROM pats_bot_synonyms WHERE activo = 1")->fetchColumn();
    }
  } catch (Throwable $e) {}

  try {
    if (catbot_rows_exist($db, 'pats_bot_training_queue')) {
      $trainCount = (int)$db->query("SELECT COUNT(*) FROM pats_bot_training_queue WHERE estado = 'PENDIENTE'")->fetchColumn();
    }
  } catch (Throwable $e) {}

  catbot_json([
    'ok' => true,
    'db' => 'PATS',
    'manual_activos' => $manualCount,
    'operativos_activos' => $opsCount,
    'sinonimos_activos' => $synCount,
    'training_pendientes' => $trainCount,
    'rol' => $rol,
    'usuario' => $userLog
  ]);
}

/* =========================================================
   SCORING
========================================================= */

function catbot_score_text(
  string $query,
  array $tokens,
  string $haystack,
  int $priority = 10,
  ?string $detectedIntent = null,
  ?string $rowIntent = null
): float {
  $q = catbot_norm($query);
  $h = catbot_norm($haystack);

  if ($q === '' || $h === '') return 0.0;

  $score = 0.0;

  if ($detectedIntent !== null && $rowIntent !== null && $detectedIntent === $rowIntent) {
    $score += 0.42;
  }

  if (catbot_contains($h, $q)) {
    $score += 0.38;
  }

  if ($tokens) {
    $hits = 0;

    foreach ($tokens as $t) {
      if ($t !== '' && catbot_contains($h, $t)) {
        $hits++;
      }
    }

    $ratio = $hits / max(1, count($tokens));
    $score += $ratio * 0.48;

    if ($hits === count($tokens)) {
      $score += 0.10;
    }
  }

  /*
    Prioridad cuenta, pero ya no puede aplastar el intent.
  */
  $score += min(0.10, max(0, $priority) / 10000);

  return min(1.0, $score);
}

/* =========================================================
   BUSCADOR MANUAL
========================================================= */

function catbot_search_manual(PDO $db, string $question, string $originalQuestion = ''): ?array {
  $qNorm = catbot_norm($question);
  $tokens = catbot_words($question);
  $shortcutQuestion = $originalQuestion !== '' ? $originalQuestion : $question;

  /*
    0) Shortcut controlado: solo si original realmente pregunta qué es PATS.
  */
  if (catbot_is_que_es_pats($shortcutQuestion)) {
    try {
      $stmt = $db->prepare("
        SELECT
          id,
          categoria,
          subcategoria,
          intent,
          pregunta_base,
          respuesta,
          respuesta_corta,
          fuente,
          prioridad
        FROM pats_bot_knowledge
        WHERE activo = 1
          AND intent = 'que_es_pats'
        ORDER BY prioridad DESC, id ASC
        LIMIT 1
      ");
      $stmt->execute();
      $row = $stmt->fetch();

      if ($row) {
        $row['source_type'] = 'MANUAL';
        $row['search_method'] = 'SHORTCUT_QUE_ES_PATS';
        $row['score'] = 1.0;
        $row['detected_intent'] = 'que_es_pats';
        return $row;
      }
    } catch (Throwable $e) {}
  }

  $detectedIntent = catbot_detect_intent_from_db($db, $question, $originalQuestion);

  /*
    Si no hay tokens y no hay intent, no dejar que PATS gane solo.
  */
  if (!$tokens && $detectedIntent === null) {
    return null;
  }

  try {
    if ($detectedIntent !== null) {
      $stmt = $db->prepare("
        SELECT
          id,
          categoria,
          subcategoria,
          intent,
          pregunta_base,
          respuesta,
          respuesta_corta,
          keywords,
          fuente,
          prioridad
        FROM pats_bot_knowledge
        WHERE activo = 1
          AND intent = :intent
        ORDER BY prioridad DESC, id ASC
        LIMIT 500
      ");
      $stmt->execute([':intent' => $detectedIntent]);
    } else {
      $stmt = $db->prepare("
        SELECT
          id,
          categoria,
          subcategoria,
          intent,
          pregunta_base,
          respuesta,
          respuesta_corta,
          keywords,
          fuente,
          prioridad
        FROM pats_bot_knowledge
        WHERE activo = 1
        ORDER BY prioridad DESC, id ASC
        LIMIT 1000
      ");
      $stmt->execute();
    }

    $rows = $stmt->fetchAll();

    $best = null;
    $bestScore = 0.0;

    foreach ($rows as $row) {
      $rowIntent = trim((string)($row['intent'] ?? ''));

      if ($detectedIntent !== null && $rowIntent !== $detectedIntent) {
        continue;
      }

      $haystack = implode(' ', [
        (string)($row['categoria'] ?? ''),
        (string)($row['subcategoria'] ?? ''),
        (string)($row['intent'] ?? ''),
        (string)($row['pregunta_base'] ?? ''),
        (string)($row['respuesta_corta'] ?? ''),
        (string)($row['keywords'] ?? ''),
        (string)($row['respuesta'] ?? '')
      ]);

      $score = catbot_score_text(
        $qNorm,
        $tokens,
        $haystack,
        (int)($row['prioridad'] ?? 10),
        $detectedIntent,
        $rowIntent
      );

      if ($score > $bestScore) {
        $bestScore = $score;
        $best = $row;
      }
    }

    if ($best && $bestScore >= 0.22) {
      unset($best['keywords']);
      $best['source_type'] = 'MANUAL';
      $best['search_method'] = $detectedIntent ? 'INTENT_SCORE' : 'PHP_SCORE';
      $best['score'] = round($bestScore, 4);
      $best['detected_intent'] = $detectedIntent;
      return $best;
    }
  } catch (Throwable $e) {}

  /*
    FULLTEXT solo como último respaldo.
  */
  try {
    $stmt = $db->prepare("
      SELECT
        id,
        categoria,
        subcategoria,
        intent,
        pregunta_base,
        respuesta,
        respuesta_corta,
        fuente,
        prioridad,
        MATCH(pregunta_base, respuesta, keywords)
        AGAINST (:q IN NATURAL LANGUAGE MODE) AS score
      FROM pats_bot_knowledge
      WHERE activo = 1
        AND MATCH(pregunta_base, respuesta, keywords)
            AGAINST (:q IN NATURAL LANGUAGE MODE)
      ORDER BY score DESC, prioridad DESC, id ASC
      LIMIT 1
    ");

    $stmt->execute([':q' => $qNorm]);
    $row = $stmt->fetch();

    if ($row) {
      $row['source_type'] = 'MANUAL';
      $row['search_method'] = 'FULLTEXT';
      $row['score'] = min(0.55, (float)($row['score'] ?? 0));
      $row['detected_intent'] = $detectedIntent;
      return $row;
    }
  } catch (Throwable $e) {}

  return null;
}

/* =========================================================
   BUSCADOR OPERATIVO
========================================================= */

function catbot_search_ops(PDO $db, string $question, string $rol, string $originalQuestion = ''): ?array {
  if (!catbot_rows_exist($db, 'pats_bot_ops_knowledge')) {
    return null;
  }

  $qNorm = catbot_norm($question);
  $tokens = catbot_words($question);
  $shortcutQuestion = $originalQuestion !== '' ? $originalQuestion : $question;

  if (catbot_is_que_es_pats($shortcutQuestion)) {
    try {
      $stmt = $db->prepare("
        SELECT
          id,
          codigo,
          id_misional,
          id_modelo,
          proceso,
          fase,
          tipo_caso,
          subtipo_caso,
          pregunta_operativa,
          situacion_usuario,
          respuesta_usuario_sugerida,
          pasos_operativos,
          validaciones,
          modulos_consultar,
          cuando_escalar,
          frases_sugeridas,
          errores_evitar,
          roles_aplica,
          nivel,
          prioridad,
          fuente
        FROM pats_bot_ops_knowledge
        WHERE activo = 1
          AND (
            tipo_caso = 'QUE_ES_PATS'
            OR codigo = 'OPS-PATS-QUE-ES-001'
            OR keywords LIKE '%que es pats%'
          )
          AND (
            roles_aplica LIKE :rolLike
            OR roles_aplica LIKE '%ADMIN%'
            OR roles_aplica LIKE '%ADMINPATS%'
          )
        ORDER BY prioridad DESC, id ASC
        LIMIT 1
      ");

      $stmt->execute([
        ':rolLike' => '%' . $rol . '%'
      ]);

      $row = $stmt->fetch();

      if ($row) {
        $row['source_type'] = 'OPERATIVO';
        $row['search_method'] = 'SHORTCUT_QUE_ES_PATS';
        $row['score'] = 1.0;
        $row['detected_intent'] = 'que_es_pats';
        return $row;
      }
    } catch (Throwable $e) {}
  }

  $detectedIntent = catbot_detect_intent_from_db($db, $question, $originalQuestion);

  if (!$tokens && $detectedIntent === null) {
    return null;
  }

  try {
    $stmt = $db->prepare("
      SELECT
        id,
        codigo,
        id_misional,
        id_modelo,
        proceso,
        fase,
        tipo_caso,
        subtipo_caso,
        pregunta_operativa,
        situacion_usuario,
        respuesta_usuario_sugerida,
        pasos_operativos,
        validaciones,
        modulos_consultar,
        cuando_escalar,
        frases_sugeridas,
        errores_evitar,
        roles_aplica,
        nivel,
        prioridad,
        keywords,
        fuente
      FROM pats_bot_ops_knowledge
      WHERE activo = 1
        AND (
          roles_aplica LIKE :rolLike
          OR roles_aplica LIKE '%ADMIN%'
          OR roles_aplica LIKE '%ADMINPATS%'
        )
      ORDER BY prioridad DESC, id ASC
      LIMIT 1000
    ");

    $stmt->execute([
      ':rolLike' => '%' . $rol . '%'
    ]);

    $rows = $stmt->fetchAll();

    $best = null;
    $bestScore = 0.0;

    foreach ($rows as $row) {
      $haystack = implode(' ', [
        (string)($row['codigo'] ?? ''),
        (string)($row['proceso'] ?? ''),
        (string)($row['fase'] ?? ''),
        (string)($row['tipo_caso'] ?? ''),
        (string)($row['subtipo_caso'] ?? ''),
        (string)($row['pregunta_operativa'] ?? ''),
        (string)($row['situacion_usuario'] ?? ''),
        (string)($row['respuesta_usuario_sugerida'] ?? ''),
        (string)($row['pasos_operativos'] ?? ''),
        (string)($row['validaciones'] ?? ''),
        (string)($row['modulos_consultar'] ?? ''),
        (string)($row['cuando_escalar'] ?? ''),
        (string)($row['frases_sugeridas'] ?? ''),
        (string)($row['errores_evitar'] ?? ''),
        (string)($row['keywords'] ?? '')
      ]);

      $score = catbot_score_text(
        $qNorm,
        $tokens,
        $haystack,
        (int)($row['prioridad'] ?? 10),
        $detectedIntent,
        null
      );

      /*
        Bonus si el intent detectado aparece en keywords/proceso/tipo.
      */
      if ($detectedIntent !== null && catbot_contains(catbot_norm($haystack), catbot_norm($detectedIntent))) {
        $score += 0.24;
      }

      /*
        Mapeos comunes intent -> tipo operativo.
      */
      $tipo = catbot_norm((string)($row['tipo_caso'] ?? ''));

      if ($detectedIntent === 'pago' && catbot_contains($tipo, 'pago')) $score += 0.26;
      if ($detectedIntent === 'falta_pago' && (catbot_contains($tipo, 'vencido') || catbot_contains($tipo, 'falta'))) $score += 0.26;
      if ($detectedIntent === 'reactivacion' && catbot_contains($tipo, 'reactiv')) $score += 0.26;
      if ($detectedIntent === 'vigencia' && catbot_contains($tipo, 'vigencia')) $score += 0.26;
      if ($detectedIntent === 'cotizacion' && catbot_contains($tipo, 'cotiz')) $score += 0.26;
      if ($detectedIntent === 'precios_servicios' && (catbot_contains($tipo, 'precio') || catbot_contains($tipo, 'cotiz'))) $score += 0.20;
      if ($detectedIntent === 'canalizar' && (catbot_contains($tipo, 'soporte') || catbot_contains($tipo, 'canal'))) $score += 0.26;

      $score = min(1.0, $score);

      if ($score > $bestScore) {
        $bestScore = $score;
        $best = $row;
      }
    }

    if ($best && $bestScore >= 0.22) {
      unset($best['keywords']);
      $best['source_type'] = 'OPERATIVO';
      $best['search_method'] = $detectedIntent ? 'INTENT_SCORE' : 'PHP_SCORE';
      $best['score'] = round($bestScore, 4);
      $best['detected_intent'] = $detectedIntent;
      return $best;
    }
  } catch (Throwable $e) {}

  return null;
}

/* =========================================================
   FLAGS
========================================================= */

function catbot_detect_flags(string $question): array {
  $q = catbot_norm($question);

  $flags = [];

  $checks = [
    'precio'       => ['precio','cuesta','costo','descuento','tabulador','cobro','cobraron','pagar','money'],
    'reactivacion' => ['reactivar','reactivacion','vencido','vencida','suspendido','suspendida','vigencia','vigente','comprobante','voucher'],
    'cotizacion'   => ['cotizar','cotizacion','cirugia','procedimiento','quirofano','paquete','lead'],
    'queja'        => ['queja','inconformidad','molesto','mal','no respetaron','cobraron mal','reclamo'],
    'no_seguro'    => ['seguro','poliza','cubre','cobertura'],
    'datos'        => ['id pats','nombre','telefono','correo','comprobante','referencia','datos']
  ];

  foreach ($checks as $flag => $terms) {
    foreach ($terms as $term) {
      if (catbot_contains($q, catbot_norm($term))) {
        $flags[] = $flag;
        break;
      }
    }
  }

  return array_values(array_unique($flags));
}

/* =========================================================
   PREPARAR PREGUNTA
========================================================= */

$questionOriginal = $question;
$questionExpanded = catbot_apply_synonyms($db, $questionOriginal);
$detectedIntentGlobal = catbot_detect_intent_from_db($db, $questionExpanded, $questionOriginal);

/* =========================================================
   DEBUG SEARCH
========================================================= */

if (isset($_GET['debug_search'])) {
  $m = catbot_search_manual($db, $questionExpanded, $questionOriginal);
  $o = catbot_search_ops($db, $questionExpanded, $rol, $questionOriginal);

  catbot_json([
    'ok' => true,
    'mode' => $mode,
    'question' => $questionOriginal,
    'normalized' => catbot_norm($questionOriginal),
    'expanded' => $questionExpanded,
    'expanded_normalized' => catbot_norm($questionExpanded),
    'tokens' => catbot_words($questionExpanded),
    'detected_intent' => $detectedIntentGlobal,
    'manual' => $m,
    'operativo' => $o
  ]);
}

/* =========================================================
   EJECUTAR BÚSQUEDAS SEGÚN MODO
========================================================= */

$manual = null;
$ops = null;

if ($mode === 'manual') {
  $manual = catbot_search_manual($db, $questionExpanded, $questionOriginal);
} elseif ($mode === 'operativo') {
  $ops = catbot_search_ops($db, $questionExpanded, $rol, $questionOriginal);
} else {
  $manual = catbot_search_manual($db, $questionExpanded, $questionOriginal);
  $ops    = catbot_search_ops($db, $questionExpanded, $rol, $questionOriginal);
}

$flags = catbot_detect_flags($questionExpanded);

$hasManual = is_array($manual);
$hasOps    = is_array($ops);

/* =========================================================
   SIN RESULTADO
========================================================= */

if (!$hasManual && !$hasOps) {
  $fallback = 'No encontré una respuesta suficientemente confiable en la base PATS. Para evitar dar información incorrecta, valida la duda con ADMINPATS o personal autorizado y deja la interacción documentada.';

  $logId = catbot_log(
    $db,
    $questionOriginal,
    $fallback,
    $detectedIntentGlobal,
    null,
    0.0,
    'SIN_RESULTADO',
    $rol,
    $userLog,
    $region,
    $unidad
  );

  catbot_queue_training(
    $db,
    $logId,
    $questionOriginal,
    $fallback,
    'SIN_RESULTADO',
    $detectedIntentGlobal,
    'SIN_RESULTADO',
    0.0
  );

  catbot_json([
    'ok' => true,
    'mode' => strtoupper($mode),
    'audience' => 'CONCIERGE_ADMISION',
    'question' => $questionOriginal,
    'expanded_question' => $questionExpanded,
    'answer' => $fallback,
    'respuesta_usuario_sugerida' => 'Permíteme validar esta información con un responsable autorizado para no darte un dato incorrecto.',
    'ruta_operativa' => 'Registrar la duda, canalizar con ADMINPATS o responsable autorizado y dar seguimiento.',
    'validaciones' => 'No confirmar beneficios, precios, vigencia, reactivaciones o excepciones sin fuente autorizada.',
    'modulos_consultar' => 'Manual PATS, PATS DROP, PATS MONEY, LEAD+ o PATS Vigencias, según corresponda.',
    'cuando_escalar' => 'Escalar cuando no exista información clara, cuando se trate de precio, vigencia, reactivación, cobro, queja o excepción.',
    'frases_sugeridas' => '',
    'errores_evitar' => 'No improvisar. No prometer cobertura. No confirmar precios o vigencia sin validar.',
    'alertas' => [
      'Esta pregunta se envió a entrenamiento supervisado como SIN_RESULTADO.'
    ],
    'intent' => $detectedIntentGlobal,
    'source' => 'SIN_RESULTADO',
    'confidence' => 0.0,
    'flags' => $flags,
    'matched' => [],
    'log_id' => $logId
  ]);
}

/* =========================================================
   ARMAR RESPUESTA
========================================================= */

$respuestaUsuario = '';
$rutaOperativa = '';
$validaciones = '';
$modulos = '';
$cuandoEscalar = '';
$frases = '';
$errores = '';
$intent = $detectedIntentGlobal;
$knowledgeId = null;
$source = '';
$confidence = 0.0;
$matched = [];

if ($hasManual) {
  $respuestaUsuario = trim((string)($manual['respuesta'] ?? ''));
  $intent = $manual['intent'] ?? $detectedIntentGlobal;
  $knowledgeId = (int)($manual['id'] ?? 0);
  $confidence = max($confidence, min(1.0, (float)($manual['score'] ?? 0.0)));
  $source = 'MANUAL_PATS';

  $matched['manual'] = [
    'id' => (int)($manual['id'] ?? 0),
    'categoria' => $manual['categoria'] ?? null,
    'subcategoria' => $manual['subcategoria'] ?? null,
    'intent' => $manual['intent'] ?? null,
    'pregunta_base' => $manual['pregunta_base'] ?? null,
    'fuente' => $manual['fuente'] ?? null,
    'search_method' => $manual['search_method'] ?? null,
    'detected_intent' => $manual['detected_intent'] ?? null,
    'score' => (float)($manual['score'] ?? 0)
  ];
}

if ($hasOps) {
  if ($mode === 'operativo' || $respuestaUsuario === '') {
    $respuestaUsuario = trim((string)($ops['respuesta_usuario_sugerida'] ?? $respuestaUsuario));
  }

  $rutaOperativa = trim((string)($ops['pasos_operativos'] ?? ''));
  $validaciones  = trim((string)($ops['validaciones'] ?? ''));
  $modulos       = trim((string)($ops['modulos_consultar'] ?? ''));
  $cuandoEscalar = trim((string)($ops['cuando_escalar'] ?? ''));
  $frases        = trim((string)($ops['frases_sugeridas'] ?? ''));
  $errores       = trim((string)($ops['errores_evitar'] ?? ''));

  if (!$knowledgeId) {
    $knowledgeId = (int)($ops['id'] ?? 0);
  }

  $confidence = max($confidence, min(1.0, (float)($ops['score'] ?? 0.0)));
  $source = $hasManual ? 'MANUAL_Y_OPERATIVO' : 'OPERATIVO_PATS';

  $matched['operativo'] = [
    'id' => (int)($ops['id'] ?? 0),
    'codigo' => $ops['codigo'] ?? null,
    'id_misional' => $ops['id_misional'] ?? null,
    'id_modelo' => $ops['id_modelo'] ?? null,
    'proceso' => $ops['proceso'] ?? null,
    'fase' => $ops['fase'] ?? null,
    'tipo_caso' => $ops['tipo_caso'] ?? null,
    'subtipo_caso' => $ops['subtipo_caso'] ?? null,
    'nivel' => $ops['nivel'] ?? null,
    'fuente' => $ops['fuente'] ?? null,
    'search_method' => $ops['search_method'] ?? null,
    'detected_intent' => $ops['detected_intent'] ?? null,
    'score' => (float)($ops['score'] ?? 0)
  ];
}

if ($respuestaUsuario === '') {
  $respuestaUsuario = 'No encontré una respuesta redactada, pero sí una coincidencia parcial. Valida la información antes de comunicarla al usuario.';
}

if ($rutaOperativa === '') {
  if ($mode === 'manual') {
    $rutaOperativa = 'Modo Manual de Usuario: usa esta respuesta como texto de apoyo para explicar al usuario. Si la duda requiere precio, vigencia, red, tabulador, reactivación o caso particular, cambia a modo operativo o valida en el módulo correspondiente.';
  } else {
    $rutaOperativa = 'Usar esta respuesta como orientación. Si la duda requiere precio, vigencia, reactivación, red, tabulador o caso individual, validar en el módulo correspondiente antes de confirmar.';
  }
}

if ($validaciones === '') {
  if ($mode === 'manual') {
    $validaciones = 'No confirmar precios, vigencia, disponibilidad, red, cotizaciones o reactivaciones solo con el manual. Para esos casos, validar en PATS MONEY, PATS Vigencias, LEAD+ o fuente autorizada.';
  } else {
    $validaciones = 'Validar estatus activo, red autorizada, tabulador vigente o datos del usuario únicamente cuando la duda lo requiera.';
  }
}

if ($modulos === '') {
  $modulos = $mode === 'manual'
    ? 'Manual PATS. Si requiere validación: PATS DROP, PATS MONEY, LEAD+ o PATS Vigencias.'
    : 'PATS DROP, PATS MONEY, LEAD+, PATS Vigencias, según corresponda.';
}

if ($cuandoEscalar === '') {
  $cuandoEscalar = 'Escalar a ADMINPATS si no hay información suficiente, si existe reclamo, inconsistencia, precio no confirmado, problema de vigencia o solicitud de excepción.';
}

/* =========================================================
   ALERTAS
========================================================= */

$alertas = [];

if (in_array('no_seguro', $flags, true)) {
  $alertas[] = 'Aclarar siempre que PATS no es un seguro médico; usar lenguaje de beneficios, descuentos o precios preferenciales.';
}

if (in_array('precio', $flags, true)) {
  $alertas[] = 'No confirmar precios de memoria. Validar en PATS MONEY, tabulador vigente o fuente autorizada.';
}

if (in_array('reactivacion', $flags, true)) {
  $alertas[] = 'Para reactivación, consultar PATS Vigencias y no cambiar estatus sin evidencia válida.';
}

if (in_array('cotizacion', $flags, true)) {
  $alertas[] = 'Para cotizaciones de cirugía/procedimiento, validar en LEAD+ y aclarar alcance de la cotización.';
}

if (in_array('queja', $flags, true)) {
  $alertas[] = 'Si hay queja o inconformidad, escuchar, registrar y canalizar; no confrontar al usuario.';
}

if (in_array('datos', $flags, true)) {
  $alertas[] = 'Pedir datos personales solo cuando exista finalidad operativa clara.';
}

if ($confidence < 0.55) {
  $alertas[] = 'Confianza media/baja: esta interacción se mandará a entrenamiento supervisado para mejorar el bot.';
}

/* =========================================================
   TEXTO CONSOLIDADO PARA LOG
========================================================= */

$answerText = "Respuesta sugerida al usuario:\n" . $respuestaUsuario .
  "\n\nRuta operativa:\n" . $rutaOperativa .
  "\n\nValidaciones:\n" . $validaciones .
  "\n\nMódulos a consultar:\n" . $modulos .
  "\n\nCuándo escalar:\n" . $cuandoEscalar;

if ($frases !== '') {
  $answerText .= "\n\nFrases sugeridas:\n" . $frases;
}

if ($errores !== '') {
  $answerText .= "\n\nErrores a evitar:\n" . $errores;
}

if ($alertas) {
  $answerText .= "\n\nAlertas del asistente:\n- " . implode("\n- ", $alertas);
}

/* =========================================================
   LOG + ENTRENAMIENTO
========================================================= */

$logId = catbot_log(
  $db,
  $questionOriginal,
  $answerText,
  $intent,
  $knowledgeId ?: null,
  $confidence,
  $source ?: 'DESCONOCIDO',
  $rol,
  $userLog,
  $region,
  $unidad
);

if ($confidence > 0 && $confidence < 0.55) {
  catbot_queue_training(
    $db,
    $logId,
    $questionOriginal,
    $answerText,
    'BAJA_CONFIANZA',
    $intent,
    $source,
    $confidence
  );
}

/* =========================================================
   RESPUESTA FINAL
========================================================= */

catbot_json([
  'ok' => true,
  'mode' => strtoupper($mode),
  'audience' => 'CONCIERGE_ADMISION',
  'question' => $questionOriginal,
  'expanded_question' => $questionExpanded,

  'answer' => $answerText,

  'respuesta_usuario_sugerida' => $respuestaUsuario,
  'ruta_operativa' => $rutaOperativa,
  'validaciones' => $validaciones,
  'modulos_consultar' => $modulos,
  'cuando_escalar' => $cuandoEscalar,
  'frases_sugeridas' => $frases,
  'errores_evitar' => $errores,
  'alertas' => $alertas,

  'intent' => $intent,
  'detected_intent' => $detectedIntentGlobal,
  'source' => $source ?: 'DESCONOCIDO',
  'confidence' => round($confidence, 4),
  'flags' => $flags,
  'matched' => $matched,
  'log_id' => $logId
]);