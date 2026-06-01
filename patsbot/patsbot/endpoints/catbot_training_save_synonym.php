<?php
/*
=========================================================
ez/patsbot/endpoints/catbot_training_save_synonym.php
Guardar sinónimo desde entrenamiento CAT BOT PATS
=========================================================
*/

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  @session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/catbot_bootstrap.php';

function train_json(array $data, int $code = 200): void {
  http_response_code($code);
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function train_fail(string $msg, int $code = 422): void {
  train_json(['ok' => false, 'error' => $msg], $code);
}

if (!isset($db_pats) || !($db_pats instanceof PDO)) {
  train_fail('No se encontró conexión PDO PATS.', 500);
}

$db = $db_pats;

$rol = strtoupper(trim((string)($CATBOT_ROLE ?? ($_SESSION['rol'] ?? ''))));
$userLog = trim((string)($CATBOT_USER ?? ($_SESSION['usuario'] ?? '')));

if (!in_array($rol, ['ADMIN', 'ADMINPATS'], true)) {
  train_fail('No tienes permiso para entrenar el CAT BOT.', 403);
}

$raw = file_get_contents('php://input');
$input = [];

if ($raw !== false && trim($raw) !== '') {
  $decoded = json_decode($raw, true);
  if (is_array($decoded)) {
    $input = $decoded;
  }
}

$queueId = (int)($input['queue_id'] ?? 0);
$from = trim((string)($input['termino_usuario'] ?? ''));
$to = trim((string)($input['termino_normalizado'] ?? ''));
$intent = trim((string)($input['intent'] ?? ''));
$tipo = strtoupper(trim((string)($input['tipo'] ?? 'JERGA_OPERATIVA')));

$tiposValidos = ['SINONIMO', 'ERROR_ESCRITURA', 'JERGA_OPERATIVA', 'MODULO', 'SERVICIO'];

if ($queueId <= 0) {
  train_fail('No se recibió ID de entrenamiento válido.');
}

if ($from === '') {
  train_fail('Captura el término usado por el usuario.');
}

if ($to === '') {
  train_fail('Captura cómo debe entenderlo el bot.');
}

if (!in_array($tipo, $tiposValidos, true)) {
  $tipo = 'JERGA_OPERATIVA';
}

try {
  $stmt = $db->prepare("
    SELECT id, pregunta, motivo, estado
    FROM pats_bot_training_queue
    WHERE id = :id
    LIMIT 1
  ");
  $stmt->execute([':id' => $queueId]);
  $queue = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$queue) {
    train_fail('No se encontró el registro de entrenamiento.', 404);
  }

  $ins = $db->prepare("
    INSERT INTO pats_bot_synonyms
    (termino_usuario, termino_normalizado, intent, tipo, activo, created_at)
    VALUES
    (:termino_usuario, :termino_normalizado, :intent, :tipo, 1, NOW())
  ");

  $ins->execute([
    ':termino_usuario' => $from,
    ':termino_normalizado' => $to,
    ':intent' => $intent !== '' ? $intent : null,
    ':tipo' => $tipo
  ]);

  $synId = (int)$db->lastInsertId();

  $upd = $db->prepare("
    UPDATE pats_bot_training_queue
    SET
      estado = 'RESUELTO',
      revisado_por = :revisado_por,
      comentario_revision = CONCAT(
        COALESCE(comentario_revision, ''),
        CASE WHEN COALESCE(comentario_revision, '') = '' THEN '' ELSE '\n' END,
        :comentario
      ),
      updated_at = NOW()
    WHERE id = :id
    LIMIT 1
  ");

  $upd->execute([
    ':revisado_por' => $userLog,
    ':comentario' => 'Se agregó sinónimo: "' . $from . '" => "' . $to . '".',
    ':id' => $queueId
  ]);

  train_json([
    'ok' => true,
    'message' => 'Sinónimo guardado y pregunta marcada como resuelta.',
    'synonym_id' => $synId,
    'queue_id' => $queueId
  ]);

} catch (Throwable $e) {
  train_fail('No fue posible guardar el sinónimo.', 500);
}