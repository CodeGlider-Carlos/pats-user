<?php
/*
=========================================================
ez/patsbot/endpoints/catbot_bootstrap.php
Bootstrap exclusivo CAT BOT PATS
PDO / BD PATS
=========================================================
*/

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  @session_start();
}

require_once __DIR__ . '/../../../varSQL/bd.php';
require_once __DIR__ . '/../../../varSQL/var.php';

if (empty($_SESSION['usuario'])) {
  http_response_code(401);
  echo json_encode([
    'ok' => false,
    'error' => 'Sesión no válida'
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

if (!isset($db_pats) || !($db_pats instanceof PDO)) {
  if (isset($servidor, $pats, $usuario, $password) && function_exists('pdo_conn')) {
    $db_pats = pdo_conn($servidor, $pats, $usuario, $password, 'PATS');
  }
}

if (!isset($db_pats) || !($db_pats instanceof PDO)) {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'error' => 'No se encontró la conexión PDO $db_pats para la base PATS.'
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

try {
  $db_pats->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db_pats->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $db_pats->exec("SET NAMES utf8mb4");
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'error' => 'No fue posible preparar la conexión PDO del CAT BOT PATS.'
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

$CATBOT_ROLE   = strtoupper(trim((string)($_SESSION['rol'] ?? '')));
$CATBOT_USER   = trim((string)($_SESSION['usuario'] ?? ''));
$CATBOT_NAME   = trim((string)($_SESSION['nombre'] ?? $CATBOT_USER));
$CATBOT_REGION = trim((string)($_SESSION['acroregion'] ?? ($_SESSION['region'] ?? '')));
$CATBOT_UNIDAD = trim((string)($_SESSION['acronu'] ?? ($_SESSION['unidad'] ?? '')));

$CATBOT_ROLES_PERMITIDOS = [
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

if (!in_array($CATBOT_ROLE, $CATBOT_ROLES_PERMITIDOS, true)) {
  http_response_code(403);
  echo json_encode([
    'ok' => false,
    'error' => 'El Asistente PATS está disponible solo para Concierge, Admisión, Caja, ADMIN o ADMINPATS.',
    'rol' => $CATBOT_ROLE
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}