<?php
/*
=========================================================
ez/patsbot/endpoints/catbot_training_publish_ops.php
Publicar conocimiento OPERATIVO desde entrenamiento CAT BOT PATS
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
  train_fail('No tienes permiso para publicar conocimiento operativo del CAT BOT.', 403);
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

$codigo = strtoupper(trim((string)($input['codigo'] ?? '')));
$proceso = trim((string)($input['proceso'] ?? 'Aprendizaje supervisado PATS'));
$fase = trim((string)($input['fase'] ?? 'Atención y orientación'));
$tipoCaso = strtoupper(trim((string)($input['tipo_caso'] ?? 'APRENDIZAJE_SUPERVISADO')));
$subtipoCaso = trim((string)($input['subtipo_caso'] ?? ''));

$preguntaOperativa = trim((string)($input['pregunta_operativa'] ?? ''));
$situacionUsuario = trim((string)($input['situacion_usuario'] ?? ''));

$respuestaUsuarioSugerida = trim((string)($input['respuesta_usuario_sugerida'] ?? ''));
$pasosOperativos = trim((string)($input['pasos_operativos'] ?? ''));
$validaciones = trim((string)($input['validaciones'] ?? ''));
$modulosConsultar = trim((string)($input['modulos_consultar'] ?? ''));
$cuandoEscalar = trim((string)($input['cuando_escalar'] ?? ''));
$frasesSugeridas = trim((string)($input['frases_sugeridas'] ?? ''));
$erroresEvitar = trim((string)($input['errores_evitar'] ?? ''));
$rolesAplica = trim((string)($input['roles_aplica'] ?? 'CON,CONCIERGE,ADM,ADMISION,CAJ,CAJA,ADMIN,ADMINPATS'));
$nivel = strtoupper(trim((string)($input['nivel'] ?? 'OPERATIVO')));
$prioridad = (int)($input['prioridad'] ?? 850);
$keywords = trim((string)($input['keywords'] ?? ''));

$nivelesValidos = ['BASICO', 'OPERATIVO', 'CRITICO'];

if (!in_array($nivel, $nivelesValidos, true)) {
  $nivel = 'OPERATIVO';
}

if ($queueId <= 0) {
  train_fail('No se recibió ID de entrenamiento válido.');
}

if ($preguntaOperativa === '') {
  train_fail('Captura la pregunta operativa.');
}

if ($respuestaUsuarioSugerida === '') {
  train_fail('Captura la respuesta sugerida al usuario.');
}

if ($pasosOperativos === '') {
  train_fail('Captura los pasos operativos.');
}

if ($validaciones === '') {
  $validaciones = 'Validar información en la fuente correspondiente antes de confirmar al usuario.';
}

if ($modulosConsultar === '') {
  $modulosConsultar = 'PATS Concierge, PATS DROP, PATS MONEY, LEAD+ o PATS Vigencias, según corresponda.';
}

if ($cuandoEscalar === '') {
  $cuandoEscalar = 'Escalar a ADMINPATS si no hay información suficiente, si existe inconsistencia, reclamo o solicitud de excepción.';
}

if ($keywords === '') {
  $keywords = implode(',', array_filter([
    $preguntaOperativa,
    $situacionUsuario,
    $tipoCaso,
    $subtipoCaso
  ]));
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

  if ($codigo === '') {
    $codigo = 'OPS-PATS-TRAIN-' . str_pad((string)$queueId, 5, '0', STR_PAD_LEFT);
  }

  /*
    Evita código duplicado si se publica dos veces.
  */
  $check = $db->prepare("
    SELECT id
    FROM pats_bot_ops_knowledge
    WHERE codigo = :codigo
    LIMIT 1
  ");
  $check->execute([':codigo' => $codigo]);
  $existing = $check->fetch(PDO::FETCH_ASSOC);

  if ($existing) {
    $codigo .= '-' . time();
  }

  $ins = $db->prepare("
    INSERT INTO pats_bot_ops_knowledge
    (
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
      fuente,
      activo
    )
    VALUES
    (
      :codigo,
      NULL,
      NULL,
      :proceso,
      :fase,
      :tipo_caso,
      :subtipo_caso,
      :pregunta_operativa,
      :situacion_usuario,
      :respuesta_usuario_sugerida,
      :pasos_operativos,
      :validaciones,
      :modulos_consultar,
      :cuando_escalar,
      :frases_sugeridas,
      :errores_evitar,
      :roles_aplica,
      :nivel,
      :prioridad,
      :keywords,
      'APRENDIZAJE_SUPERVISADO',
      1
    )
  ");

  $ins->execute([
    ':codigo' => $codigo,
    ':proceso' => $proceso,
    ':fase' => $fase,
    ':tipo_caso' => $tipoCaso,
    ':subtipo_caso' => $subtipoCaso !== '' ? $subtipoCaso : null,
    ':pregunta_operativa' => $preguntaOperativa,
    ':situacion_usuario' => $situacionUsuario !== '' ? $situacionUsuario : null,
    ':respuesta_usuario_sugerida' => $respuestaUsuarioSugerida,
    ':pasos_operativos' => $pasosOperativos,
    ':validaciones' => $validaciones,
    ':modulos_consultar' => $modulosConsultar,
    ':cuando_escalar' => $cuandoEscalar,
    ':frases_sugeridas' => $frasesSugeridas !== '' ? $frasesSugeridas : null,
    ':errores_evitar' => $erroresEvitar !== '' ? $erroresEvitar : null,
    ':roles_aplica' => $rolesAplica,
    ':nivel' => $nivel,
    ':prioridad' => $prioridad,
    ':keywords' => $keywords
  ]);

  $opsId = (int)$db->lastInsertId();

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
    ':comentario_revision' => 'Se publicó respuesta Operativa en pats_bot_ops_knowledge ID ' . $opsId . ' código ' . $codigo . '.',
    ':id' => $queueId
  ]);

  $db->commit();

  train_json([
    'ok' => true,
    'message' => 'Respuesta Operativa publicada correctamente.',
    'queue_id' => $queueId,
    'ops_id' => $opsId,
    'codigo' => $codigo
  ]);

} catch (Throwable $e) {
  if ($db->inTransaction()) {
    $db->rollBack();
  }

  train_fail('No fue posible publicar la respuesta Operativa.', 500);
}