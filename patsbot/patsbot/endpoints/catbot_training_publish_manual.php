<?php
/*
=========================================================
ez/patsbot/endpoints/catbot_training_publish_manual.php
Publicar conocimiento MANUAL desde entrenamiento CAT BOT PATS
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

try {
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $db->exec("SET NAMES utf8mb4");
} catch (Throwable $e) {
  train_fail('No fue posible preparar la conexión PATS.', 500);
}

$rol = strtoupper(trim((string)($CATBOT_ROLE ?? ($_SESSION['rol'] ?? ''))));
$userLog = trim((string)($CATBOT_USER ?? ($_SESSION['usuario'] ?? '')));

if (!in_array($rol, ['ADMIN', 'ADMINPATS'], true)) {
  train_fail('No tienes permiso para publicar conocimiento del CAT BOT.', 403);
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

$categoria = trim((string)($input['categoria'] ?? 'Aprendizaje supervisado'));
$subcategoria = trim((string)($input['subcategoria'] ?? 'Respuesta aprobada'));
$intent = trim((string)($input['intent'] ?? 'aprendizaje_supervisado'));
$preguntaBase = trim((string)($input['pregunta_base'] ?? ''));
$respuesta = trim((string)($input['respuesta'] ?? ''));
$respuestaCorta = trim((string)($input['respuesta_corta'] ?? ''));
$keywords = trim((string)($input['keywords'] ?? ''));
$prioridad = (int)($input['prioridad'] ?? 850);

$requiereLogin = (int)($input['requiere_login'] ?? 0);
$requiereDatosUsuario = (int)($input['requiere_datos_usuario'] ?? 0);

if ($queueId <= 0) {
  train_fail('No se recibió ID de entrenamiento válido.');
}

if ($preguntaBase === '') {
  train_fail('Captura la pregunta base para el Manual.');
}

if ($respuesta === '') {
  train_fail('Captura la respuesta que debe usar el Manual.');
}

if ($respuestaCorta === '') {
  $respuestaCorta = mb_substr($respuesta, 0, 240, 'UTF-8');
}

if ($keywords === '') {
  $keywords = $preguntaBase;
}

if ($prioridad <= 0) {
  $prioridad = 850;
}

try {
  $db->beginTransaction();

  $stmt = $db->prepare("
    SELECT id, pregunta, motivo, estado
    FROM pats_bot_training_queue
    WHERE id = :id
    LIMIT 1
  ");
  $stmt->execute([':id' => $queueId]);
  $queue = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$queue) {
    $db->rollBack();
    train_fail('No se encontró el registro de entrenamiento.', 404);
  }

  $ins = $db->prepare("
    INSERT INTO pats_bot_knowledge
    (
      categoria,
      subcategoria,
      intent,
      pregunta_base,
      respuesta,
      keywords,
      respuesta_corta,
      requiere_login,
      requiere_datos_usuario,
      prioridad,
      activo,
      fuente
    )
    VALUES
    (
      :categoria,
      :subcategoria,
      :intent,
      :pregunta_base,
      :respuesta,
      :keywords,
      :respuesta_corta,
      :requiere_login,
      :requiere_datos_usuario,
      :prioridad,
      1,
      'APRENDIZAJE_SUPERVISADO'
    )
  ");

  $ins->execute([
    ':categoria' => $categoria,
    ':subcategoria' => $subcategoria,
    ':intent' => $intent,
    ':pregunta_base' => $preguntaBase,
    ':respuesta' => $respuesta,
    ':keywords' => $keywords,
    ':respuesta_corta' => $respuestaCorta,
    ':requiere_login' => $requiereLogin ? 1 : 0,
    ':requiere_datos_usuario' => $requiereDatosUsuario ? 1 : 0,
    ':prioridad' => $prioridad
  ]);

  $knowledgeId = (int)$db->lastInsertId();

  $upd = $db->prepare("
    UPDATE pats_bot_training_queue
    SET
      estado = 'RESUELTO',
      revisado_por = :revisado_por,
      comentario_revision = :comentario_revision,
      updated_at = NOW()
    WHERE id = :id
    LIMIT 1
  ");

  $upd->execute([
    ':revisado_por' => $userLog,
    ':comentario_revision' => 'Se publicó respuesta Manual en pats_bot_knowledge ID ' . $knowledgeId . '.',
    ':id' => $queueId
  ]);

  $db->commit();

  train_json([
    'ok' => true,
    'message' => 'Respuesta Manual publicada correctamente.',
    'queue_id' => $queueId,
    'knowledge_id' => $knowledgeId
  ]);

} catch (Throwable $e) {
  if ($db->inTransaction()) {
    $db->rollBack();
  }

  train_fail('No fue posible publicar la respuesta Manual.', 500);
}