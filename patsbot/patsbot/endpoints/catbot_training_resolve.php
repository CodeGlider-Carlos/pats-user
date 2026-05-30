<?php
/*
=========================================================
ez/patsbot/endpoints/catbot_training_resolve.php
Marcar entrenamiento como resuelto
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
$comentario = trim((string)($input['comentario'] ?? 'Marcado como resuelto desde panel de entrenamiento.'));

if ($queueId <= 0) {
  train_fail('No se recibió ID de entrenamiento válido.');
}

try {
  $stmt = $db->prepare("
    UPDATE pats_bot_training_queue
    SET
      estado = 'RESUELTO',
      revisado_por = :revisado_por,
      comentario_revision = :comentario_revision,
      updated_at = NOW()
    WHERE id = :id
    LIMIT 1
  ");

  $stmt->execute([
    ':revisado_por' => $userLog,
    ':comentario_revision' => $comentario,
    ':id' => $queueId
  ]);

  train_json([
    'ok' => true,
    'message' => 'Pregunta marcada como resuelta.',
    'queue_id' => $queueId
  ]);

} catch (Throwable $e) {
  train_fail('No fue posible marcar como resuelto.', 500);
}