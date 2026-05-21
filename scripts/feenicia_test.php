<?php

/**
 * Manual Test Case — Feenicia
 * Ejecuta los 5 pasos del flujo de venta manual y captura
 * request + response reales para llenar la matriz de pruebas.
 */

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app    = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Feenicia\FeeniciaCryptoService;
use App\Services\Feenicia\FeeniciaHttpClient;
use App\Exceptions\Feenicia\FeeniciaException;

/** @var FeeniciaCryptoService $crypto */
$crypto = $app->make(FeeniciaCryptoService::class);
/** @var FeeniciaHttpClient $http */
$http = $app->make(FeeniciaHttpClient::class);

// Variables de estado entre pasos
$orderId       = null;
$transactionId = null;
$authnum       = null;
$receiptId     = null;

$results = [];

// ── Helper ───────────────────────────────────────────────────────────────────
// Cada $fn retorna [$payload, $response, $success, $error|null]
// para que el request siempre se capture aunque haya excepción.
function runStep(string $name, callable $fn, array &$results): array
{
    $dt    = new DateTime();
    $start = microtime(true);

    [$requestPayload, $response, $ok, $err] = $fn();

    $elapsed = (int) round((microtime(true) - $start) * 1000);
    $code    = $response['responseCode'] ?? $response['ResponseCode'] ?? null;
    $status  = ($ok && ($code === '00' || $code === null)) ? 'SUCCESS' : 'FAIL';

    $row = [
        'name'       => $name,
        'date'       => $dt->format('d/m/Y'),
        'hour'       => $dt->format('H:i:s'),
        'request'    => $requestPayload,
        'response'   => $response,
        'result'     => $status,
        'elapsed_ms' => $elapsed,
        'error'      => $err,
    ];

    $results[] = $row;
    $icon      = $status === 'SUCCESS' ? '✓' : '✗';
    echo sprintf("[%s] %-20s → %s (%d ms)\n", $icon, $name, $status, $elapsed);
    if ($err) echo "    ERROR: {$err}\n";

    return $row;
}

// Ejecuta HTTP y siempre devuelve [$payload, $response, $ok, $error]
function call(FeeniciaHttpClient $http, string $endpoint, array $payload): array
{
    try {
        $response = $http->post($endpoint, $payload);
        return [$payload, $response, true, null];
    } catch (FeeniciaException $e) {
        return [$payload, ['responseCode' => $e->responseCode, 'message' => $e->getMessage()], false, $e->getMessage()];
    } catch (\Throwable $e) {
        return [$payload, ['error' => $e->getMessage()], false, $e->getMessage()];
    }
}

echo "\n=== Manual Test Case — Feenicia ===\n\n";

// ════════════════════════════════════════════════════════════════════════════
// PASO 1 — Generate Order Sale
// Endpoint: /receipt/order/create
// Payload simple sin cifrado, sin items array
// ════════════════════════════════════════════════════════════════════════════
runStep('Generate Order Sale', function () use ($http, &$orderId): array {

    $payload = [
        'merchant'    => config('feenicia.merchant'),
        'affiliation' => config('feenicia.affiliation'),
        'amount'      => 1.00,
        'description' => 'Membresía PATS Mensual',
        'quantity'    => 1,
        'price'       => 1.00,
    ];

    [$p, $resp, $ok, $err] = call($http, config('feenicia.endpoints.create_order'), $payload);
    if ($ok) $orderId = $resp['orderId'] ?? null;

    return [$p, $resp, $ok, $err];

}, $results);

echo "  orderId: {$orderId}\n\n";

// ════════════════════════════════════════════════════════════════════════════
// PASO 2 — Signature Save
// Endpoint: /receipt/signature/save
// Cifrar: pan, cvv2, expDate, cardholderName
// ════════════════════════════════════════════════════════════════════════════
runStep('Signature Save', function () use ($http, $crypto, $orderId, &$transactionId, &$authnum): array {

    $payload = [
        'affiliation'     => config('feenicia.affiliation'),
        'orderId'         => $orderId,
        'amount'          => 1.00,
        'transactionDate' => (int) (microtime(true) * 1000),
        'pan'             => $crypto->encrypt('5439240350653004'),
        'cardholderName'  => $crypto->encrypt('PASAPORTE'),
        'cvv2'            => $crypto->encrypt('123'),
        'expDate'         => $crypto->encrypt('2701'),
        'userId'          => config('feenicia.user'),
        'tip'             => '0.0',
    ];

    [$p, $resp, $ok, $err] = call($http, config('feenicia.endpoints.save_signature'), $payload);
    if ($ok) {
        $transactionId = $resp['transactionId'] ?? null;
        $authnum       = $resp['authnum'] ?? $resp['authNum'] ?? null;
    }

    return [$p, $resp, $ok, $err];

}, $results);

echo "  transactionId: {$transactionId}, authnum: {$authnum}\n\n";

// ════════════════════════════════════════════════════════════════════════════
// PASO 3 — Manual Save
// Endpoint: /v1/atna/sale/oneStepSaleManual
// Campos sensibles cifrados con aesRequest
// ════════════════════════════════════════════════════════════════════════════
runStep('Manual Save', function () use ($http, $crypto, &$transactionId, &$authnum, &$orderId, &$receiptId): array {

    $payload = [
        'affiliation'     => config('feenicia.affiliation'),
        'amount'          => 1.00,
        'transactionDate' => (int) (microtime(true) * 1000),
        'pan'             => $crypto->encrypt('5439240350653004'),
        'cardholderName'  => $crypto->encrypt('PASAPORTE'),
        'cvv2'            => $crypto->encrypt('123'),
        'expDate'         => $crypto->encrypt('2701'),
        'userId'          => config('feenicia.user'),
        'tip'             => '0.0',
    ];

    [$p, $resp, $ok, $err] = call($http, config('feenicia.endpoints.sale_one_step'), $payload);

    if ($ok) {
        if (empty($transactionId)) $transactionId = $resp['transactionId'] ?? null;
        if (empty($authnum))       $authnum        = $resp['authnum'] ?? $resp['authNum'] ?? null;
        if (empty($orderId))       $orderId        = $resp['orderId'] ?? null;
        $receiptId = $resp['ReciboId'] ?? $resp['receiptId'] ?? null;
    }

    return [$p, $resp, $ok, $err];

}, $results);

echo "  transactionId: {$transactionId}, authnum: {$authnum}, orderId: {$orderId}\n\n";

// ════════════════════════════════════════════════════════════════════════════
// PASO 4 — Create Receipt
// Endpoint: /receipt/receipt/CreateReceipt
// ════════════════════════════════════════════════════════════════════════════
runStep('Create Receipt', function () use ($http, $orderId, $transactionId, &$receiptId): array {

    $payload = [
        'OrderId'         => (string) $orderId,
        'TransactionId'   => (int) $transactionId,
        'Total'           => 0.0,
        'ReceiptDateTime' => '0001-01-01T00:00:00',
        'SendUrlByMail'   => false,
        'Propina'         => 0.0,
    ];

    [$p, $resp, $ok, $err] = call($http, config('feenicia.endpoints.create_receipt'), $payload);
    if ($ok) $receiptId = $resp['receiptId'] ?? $resp['ReceiptId'] ?? $receiptId;

    return [$p, $resp, $ok, $err];

}, $results);

echo "  receiptId: {$receiptId}\n\n";

// ════════════════════════════════════════════════════════════════════════════
// PASO 5 — Send Receipt
// Endpoint: /receipt/receipt/SendReceipt
// Cifrar: Email (array con un elemento)
// ════════════════════════════════════════════════════════════════════════════
runStep('Send Receipt', function () use ($http, $crypto, $receiptId): array {

    $payload = [
        'receiptId' => $receiptId,
        'Email'     => [$crypto->encrypt('prueba@ejemplo.com')],
    ];

    return call($http, config('feenicia.endpoints.send_receipt'), $payload);

}, $results);

// ════════════════════════════════════════════════════════════════════════════
// Guardar resultados
// ════════════════════════════════════════════════════════════════════════════
$outputPath = __DIR__ . '/feenicia_test_results.json';
file_put_contents($outputPath, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$success = count(array_filter($results, fn($r) => $r['result'] === 'SUCCESS'));

echo "\n=== Resumen ===\n";
foreach ($results as $r) {
    printf("[%s] %-20s %s\n", $r['result'] === 'SUCCESS' ? '✓' : '✗', $r['name'], $r['result']);
}
echo "\n{$success}/5 pasos exitosos\n";
echo "Resultados: {$outputPath}\n";
