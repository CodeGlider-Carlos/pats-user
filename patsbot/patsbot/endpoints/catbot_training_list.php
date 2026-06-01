<?php
/*
=========================================================
ez/patsbot/endpoints/catbot_training_list.php
Lista cola de entrenamiento CAT BOT PATS
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
  train_json([
    'ok' => false,
    'error' => $msg
  ], $code);
}

if (!isset($db_pats) || !($db_pats instanceof PDO)) {
  train_fail('No se encontró conexión PDO PATS.', 500);
}

$db = $db_pats;

$rol = strtoupper(trim((string)($CATBOT_ROLE ?? ($_SESSION['rol'] ?? ''))));

if (!in_array($rol, ['ADMIN', 'ADMINPATS'], true)) {
  train_fail('No tienes permiso para revisar entrenamiento del CAT BOT.', 403);
}

$motivo = strtoupper(trim((string)($_GET['motivo'] ?? '')));
$estado = strtoupper(trim((string)($_GET['estado'] ?? 'PENDIENTE')));
$q      = trim((string)($_GET['q'] ?? ''));

$motivosValidos = [
  'SIN_RESULTADO',
  'BAJA_CONFIANZA',
  'INCORRECTO',
  'CONFUSO',
  'NO_UTIL',
  'REPETIDA'
];

$estadosValidos = [
  'PENDIENTE',
  'EN_REVISION',
  'APROBADO',
  'DESCARTADO',
  'RESUELTO'
];

$where = [];
$params = [];

if ($motivo !== '' && in_array($motivo, $motivosValidos, true)) {
  $where[] = 'motivo = :motivo';
  $params[':motivo'] = $motivo;
}

if ($estado !== '' && in_array($estado, $estadosValidos, true)) {
  $where[] = 'estado = :estado';
  $params[':estado'] = $estado;
}

if ($q !== '') {
  $where[] = '(pregunta LIKE :q OR respuesta_actual LIKE :q OR intent_detectado LIKE :q OR source LIKE :q)';
  $params[':q'] = '%' . $q . '%';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

try {
  $stmt = $db->prepare("
    SELECT
      id,
      log_id,
      pregunta,
      respuesta_actual,
      motivo,
      intent_detectado,
      source,
      score,
      veces_detectada,
      estado,
      revisado_por,
      comentario_revision,
      created_at,
      updated_at
    FROM pats_bot_training_queue
    $whereSql
    ORDER BY 
      CASE estado
        WHEN 'PENDIENTE' THEN 1
        WHEN 'EN_REVISION' THEN 2
        ELSE 3
      END,
      veces_detectada DESC,
      updated_at DESC
    LIMIT 100
  ");

  $stmt->execute($params);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $summary = [
    'pendientes' => 0,
    'sin_resultado' => 0,
    'baja_confianza' => 0,
    'feedback_negativo' => 0,
    'repetidas' => 0
  ];

  try {
    $s = $db->query("
      SELECT motivo, estado, COUNT(*) AS total
      FROM pats_bot_training_queue
      GROUP BY motivo, estado
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($s as $r) {
      $m = strtoupper((string)$r['motivo']);
      $e = strtoupper((string)$r['estado']);
      $t = (int)$r['total'];

      if ($e === 'PENDIENTE') {
        $summary['pendientes'] += $t;
      }

      if ($m === 'SIN_RESULTADO' && $e === 'PENDIENTE') {
        $summary['sin_resultado'] += $t;
      }

      if ($m === 'BAJA_CONFIANZA' && $e === 'PENDIENTE') {
        $summary['baja_confianza'] += $t;
      }

      if (in_array($m, ['INCORRECTO', 'CONFUSO', 'NO_UTIL'], true) && $e === 'PENDIENTE') {
        $summary['feedback_negativo'] += $t;
      }
    }

    $rpt = $db->query("
      SELECT COUNT(*) AS total
      FROM pats_bot_training_queue
      WHERE veces_detectada > 1
        AND estado = 'PENDIENTE'
    ")->fetch(PDO::FETCH_ASSOC);

    $summary['repetidas'] = (int)($rpt['total'] ?? 0);

  } catch (Throwable $e) {
    // no romper lista
  }

  train_json([
    'ok' => true,
    'items' => $rows,
    'summary' => $summary
  ]);

} catch (Throwable $e) {
  train_fail('No fue posible cargar la cola de entrenamiento.', 500);
}