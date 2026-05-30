<?php
/*
=========================================================
ez/patsbot/endpoints/catbot_feedback.php
CAT BOT PATS - Feedback de respuestas
BD PATS mediante $db_pats
=========================================================
*/

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  @session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/catbot_bootstrap.php';

/* =========================================================
   JSON HELPERS
========================================================= */

function catbot_feedback_json(array $data, int $code = 200): void {
  http_response_code($code);
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function catbot_feedback_fail(string $msg, int $code = 422, array $extra = []): void {
  catbot_feedback_json(array_merge([
    'ok' => false,
    'error' => $msg
  ], $extra), $code);
}

/* =========================================================
   VALIDAR CONEXIÓN
========================================================= */

if (!isset($db_pats) || !($db_pats instanceof PDO)) {
  catbot_feedback_fail('No se encontró la conexión PDO $db_pats para la base PATS.', 500);
}

$db = $db_pats;

try {
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $db->exec("SET NAMES utf8mb4");
} catch (Throwable $e) {
  catbot_feedback_fail('No fue posible preparar la conexión del feedback CAT BOT PATS.', 500);
}
try {
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $db->exec("SET NAMES utf8mb4");
} catch (Throwable $e) {
  catbot_feedback_fail('No fue posible preparar la conexión del feedback CAT BOT PATS.', 500);
}

/* =========================================================
   ENVIAR FEEDBACK NEGATIVO A COLA DE ENTRENAMIENTO
========================================================= */

function catbot_feedback_queue_training(
  PDO $db,
  int $logId,
  string $motivo,
  string $comentario = ''
): void {
  $map = [
    'INCORRECTO' => 'INCORRECTO',
    'CONFUSO' => 'CONFUSO',
    'NO_UTIL' => 'NO_UTIL'
  ];

  if (!isset($map[$motivo])) {
    return;
  }

  try {
    $stmt = $db->prepare("
      SELECT
        id,
        pregunta,
        respuesta,
        intent_detectado,
        origen_respuesta,
        score
      FROM pats_bot_logs
      WHERE id = :id
      LIMIT 1
    ");
    $stmt->execute([':id' => $logId]);
    $log = $stmt->fetch();

    if (!$log) return;

    $pregunta = trim((string)($log['pregunta'] ?? ''));
    $respuesta = trim((string)($log['respuesta'] ?? ''));

    if ($comentario !== '') {
      $respuesta .= "\n\nComentario del evaluador:\n" . $comentario;
    }

    $find = $db->prepare("
      SELECT id
      FROM pats_bot_training_queue
      WHERE estado = 'PENDIENTE'
        AND log_id = :log_id
        AND motivo = :motivo
      LIMIT 1
    ");

    $find->execute([
      ':log_id' => $logId,
      ':motivo' => $map[$motivo]
    ]);

    $existing = $find->fetch();

    if ($existing) {
      $upd = $db->prepare("
        UPDATE pats_bot_training_queue
        SET
          veces_detectada = veces_detectada + 1,
          respuesta_actual = :respuesta_actual,
          updated_at = NOW()
        WHERE id = :id
        LIMIT 1
      ");

      $upd->execute([
        ':respuesta_actual' => $respuesta,
        ':id' => (int)$existing['id']
      ]);

      return;
    }

    $ins = $db->prepare("
      INSERT INTO pats_bot_training_queue
      (
        log_id,
        pregunta,
        respuesta_actual,
        motivo,
        intent_detectado,
        source,
        score,
        veces_detectada,
        estado
      )
      VALUES
      (
        :log_id,
        :pregunta,
        :respuesta_actual,
        :motivo,
        :intent_detectado,
        :source,
        :score,
        1,
        'PENDIENTE'
      )
    ");

    $ins->execute([
      ':log_id' => $logId,
      ':pregunta' => $pregunta,
      ':respuesta_actual' => $respuesta,
      ':motivo' => $map[$motivo],
      ':intent_detectado' => $log['intent_detectado'] ?? null,
      ':source' => $log['origen_respuesta'] ?? null,
      ':score' => $log['score'] ?? null
    ]);

  } catch (Throwable $e) {
    // No romper feedback por falla de cola
  }
}

/* =========================================================
   SESIÓN / ROLES
========================================================= */
/* =========================================================
   SESIÓN / ROLES
========================================================= */

$rol      = strtoupper(trim((string)($_SESSION['rol'] ?? '')));
$userLog  = trim((string)($_SESSION['usuario'] ?? ''));
$region   = trim((string)($_SESSION['acroregion'] ?? ''));
$unidad   = trim((string)($_SESSION['acronu'] ?? ''));

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
  catbot_feedback_fail('No tienes permiso para registrar feedback del Asistente PATS.', 403);
}

/* =========================================================
   INPUT
========================================================= */

$raw = file_get_contents('php://input');
$input = [];

if ($raw !== false && trim($raw) !== '') {
  $decoded = json_decode($raw, true);
  if (is_array($decoded)) {
    $input = $decoded;
  }
}

$logId = (int)($input['log_id'] ?? $_POST['log_id'] ?? 0);
$valor = strtoupper(trim((string)($input['valor'] ?? $_POST['valor'] ?? '')));
$comentario = trim((string)($input['comentario'] ?? $_POST['comentario'] ?? ''));

$valoresPermitidos = ['UTIL', 'NO_UTIL', 'INCORRECTO', 'CONFUSO'];

if ($logId <= 0) {
  catbot_feedback_fail('No se recibió un log válido para registrar el feedback.');
}

if (!in_array($valor, $valoresPermitidos, true)) {
  catbot_feedback_fail('El valor de feedback no es válido.', 422, [
    'permitidos' => $valoresPermitidos
  ]);
}

if (mb_strlen($comentario, 'UTF-8') > 1000) {
  catbot_feedback_fail('El comentario es demasiado largo. Máximo 1000 caracteres.');
}

/* =========================================================
   VALIDAR QUE EL LOG EXISTA
========================================================= */

try {
  $stmt = $db->prepare("
    SELECT id, usuario, rol, region, unidad, pregunta, created_at
    FROM pats_bot_logs
    WHERE id = :id
    LIMIT 1
  ");
  $stmt->execute([':id' => $logId]);
  $log = $stmt->fetch();

  if (!$log) {
    catbot_feedback_fail('No se encontró el registro de conversación para asociar el feedback.', 404);
  }
} catch (Throwable $e) {
  catbot_feedback_fail('No fue posible validar el log del Asistente PATS.', 500);
}

/* =========================================================
   INSERTAR FEEDBACK
========================================================= */

try {
  $stmt = $db->prepare("
    INSERT INTO pats_bot_feedback
    (log_id, usuario, valor, comentario, created_at)
    VALUES
    (:log_id, :usuario, :valor, :comentario, NOW())
  ");

  $stmt->execute([
    ':log_id' => $logId,
    ':usuario' => $userLog,
    ':valor' => $valor,
    ':comentario' => $comentario !== '' ? $comentario : null
  ]);

$feedbackId = (int)$db->lastInsertId();

if (in_array($valor, ['INCORRECTO', 'CONFUSO', 'NO_UTIL'], true)) {
  catbot_feedback_queue_training($db, $logId, $valor, $comentario);
}

catbot_feedback_json([
  'ok' => true,
  'message' => 'Feedback registrado correctamente.',
  'feedback_id' => $feedbackId,
  'log_id' => $logId,
  'valor' => $valor
]);

} catch (Throwable $e) {
  catbot_feedback_fail('No fue posible registrar el feedback del Asistente PATS.', 500);
}