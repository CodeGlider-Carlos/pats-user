<?php

/**
 * Balder Test Case — Feenicia Tokenización
 * Ejecuta todos los sub-casos de la hoja "Balder test case" y guarda
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

$results = [];

// Credenciales
$merchant    = config('feenicia.merchant');
$affiliation = config('feenicia.affiliation');
$userId      = config('feenicia.user');

// Estado compartido entre pasos
$balderKey      = null;
$cardToken      = null;  // token generado para la tarjeta QA
$saleTokenTxId  = null;  // transactionId de la venta con token
$saleAuthnum    = null;
$saleOrderId    = null;
$saleTxDate     = null;

// ── Helpers ──────────────────────────────────────────────────────────────────
function httpCall(FeeniciaHttpClient $http, string $method, string $endpoint, array $payload): array
{
    try {
        $response = $method === 'GET'
            ? $http->get($endpoint, $payload)
            : $http->post($endpoint, $payload);
        return [$payload, $response, true, null];
    } catch (FeeniciaException $e) {
        return [$payload, ['responseCode' => $e->responseCode, 'message' => $e->getMessage()], false, $e->getMessage()];
    } catch (\Throwable $e) {
        return [$payload, ['error' => $e->getMessage()], false, $e->getMessage()];
    }
}

function record(
    string $name,
    string $description,
    string $execComments,
    string $descResult,
    array  $payload,
    array  $response,
    bool   $ok,
    ?string $error,
    array  &$results
): array {
    $dt      = new DateTime();
    $code    = $response['responseCode'] ?? $response['ResponseCode'] ?? null;
    $success = $ok && ($code === '00' || $code === null);
    $result  = $success ? 'SUCCESS' : 'FAIL';

    $row = [
        'name'             => $name,
        'description'      => $description,
        'execComments'     => $execComments,
        'date'             => $dt->format('d/m/Y'),
        'hour'             => $dt->format('H:i:s'),
        'descriptionResult'=> $descResult,
        'result'           => $result,
        'request'          => $payload,
        'response'         => $response,
        'error'            => $error,
    ];

    $results[] = $row;
    $icon = $result === 'SUCCESS' ? '✓' : '✗';
    echo sprintf("  [%s] %-45s → %s\n", $icon, $name, $result);
    if ($error) echo "       ERROR: {$error}\n";

    return $row;
}

function naStep(string $name, string $description, string $reason, array &$results): void
{
    $dt = new DateTime();
    $results[] = [
        'name'             => $name,
        'description'      => $description,
        'execComments'     => $reason,
        'date'             => $dt->format('d/m/Y'),
        'hour'             => $dt->format('H:i:s'),
        'descriptionResult'=> 'N/A',
        'result'           => 'N/A',
        'request'          => [],
        'response'         => [],
        'error'            => null,
    ];
    echo "  [–] " . str_pad($name, 45) . " → N/A\n";
}

echo "\n=== Balder Test Case — Feenicia ===\n\n";

// ════════════════════════════════════════════════════════════════════════════
// SIGNUP — Registro de usuario en Balder
// Endpoint: POST /balder/auth/signup
// ════════════════════════════════════════════════════════════════════════════
echo "[ SIGNUP ]\n";

$signupPayload = [
    'merchant'    => $merchant,
    'affiliation' => $affiliation,
    'userId'      => $userId,
    'email'       => 'prueba@pasaportesalud.com',  // Balder requiere email válido
    'name'        => 'Pasaporte a tu Salud QA',
];

// Happy path
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_signup'), $signupPayload);
record('Signup', 'BALDER USER REGISTRATION', 'To start the registration of new users. We need a user, mail and application token.', 'Success registration in Balder', $p, $r, $ok, $err, $results);

// Duplicate user — must FAIL
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_signup'), $signupPayload);
// For duplicate test, success = Feenicia returned an error (i.e., correctly rejected it)
$code    = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isDupOk = !$ok || ($code !== '00');
$dt = new DateTime();
$results[] = [
    'name'             => 'Signup — same user',
    'description'      => 'BALDER USER REGISTRATION',
    'execComments'     => 'Register the same user',
    'date'             => $dt->format('d/m/Y'),
    'hour'             => $dt->format('H:i:s'),
    'descriptionResult'=> 'The registration of the same user is not allowed',
    'result'           => $isDupOk ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $err,
];
echo sprintf("  [%s] %-45s → %s\n", $isDupOk ? '✓' : '✗', 'Signup — same user (expect error)', $isDupOk ? 'SUCCESS' : 'FAIL');

// ════════════════════════════════════════════════════════════════════════════
// USER KEYS — Obtener llave pública de Balder
// Endpoint: GET /balder/auth/getKey
// ════════════════════════════════════════════════════════════════════════════
echo "\n[ USER KEYS ]\n";

[$p, $r, $ok, $err] = httpCall($http, 'GET', config('feenicia.endpoints.token_get_key'), [
    'affiliation' => $affiliation,
    'userId'      => $userId,
]);
if ($ok) $balderKey = $r['key'] ?? $r['publicKey'] ?? null;
record('User Keys', 'OBTAINING THE USER KEYS', 'For each registered user, you must obtain the corresponding keys.', 'The corresponding keys of the registered user', $p, $r, $ok, $err, $results);
echo "  balderKey: " . ($balderKey ? substr($balderKey, 0, 30) . '...' : 'null') . "\n";

// ════════════════════════════════════════════════════════════════════════════
// GENERATE TOKEN — Tokenizar tarjeta
// Endpoint: POST /balder/token/generateToken
// ════════════════════════════════════════════════════════════════════════════
echo "\n[ GENERATE TOKEN ]\n";

$generatePayload = [
    'pan'            => $crypto->encrypt('5439240350653004'),
    'cardholderName' => $crypto->encrypt('PASAPORTE'),
    'expDate'        => $crypto->encrypt('2701'),
    'cvv2'           => $crypto->encrypt('123'),
    'affiliation'    => $affiliation,
];

// Happy path
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_generate'), $generatePayload);
if ($ok) $cardToken = $r['token'] ?? null;
record('Generate Token', 'GENERATE CARD TOKEN', 'Generates a cardholder data token of each registered user on Balder. Cipher: pan, cardholderName, expDate, cvv2.', 'Deliver the cardholder data token', $p, $r, $ok, $err, $results);
echo "  cardToken: " . ($cardToken ? substr($cardToken, 0, 20) . '...' : 'null') . "\n";

// Generate same card again — expect error
[$p, $r, $ok2, $err2] = httpCall($http, 'POST', config('feenicia.endpoints.token_generate'), $generatePayload);
$code2    = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isDupOk2 = !$ok2 || ($code2 !== '00');
$dt2 = new DateTime();
$results[] = [
    'name'             => 'Generate Token — same card again',
    'description'      => 'GENERATE CARD TOKEN',
    'execComments'     => 'Generate the card token again',
    'date'             => $dt2->format('d/m/Y'),
    'hour'             => $dt2->format('H:i:s'),
    'descriptionResult'=> 'Error obtained at the generation of the cardholder data token',
    'result'           => $isDupOk2 ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $err2,
];
echo sprintf("  [%s] %-45s → %s\n", $isDupOk2 ? '✓' : '✗', 'Generate Token — same card (expect error)', $isDupOk2 ? 'SUCCESS' : 'FAIL');

// ════════════════════════════════════════════════════════════════════════════
// REGISTER MERCHANT — Registrar merchant en Balder
// Endpoint: POST /balder/auth/registerMerchantFeenicia
// ════════════════════════════════════════════════════════════════════════════
echo "\n[ REGISTER MERCHANT ]\n";

$registerPayload = [
    'merchant'    => $merchant,
    'affiliation' => $affiliation,
    'userId'      => $userId,
];

// Happy path
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_register'), $registerPayload);
record('Register Merchant', "MERCHANT REGISTER", "Merchant's registration at Balder with the data sent by Administrator.", 'To get the TokenStore', $p, $r, $ok, $err, $results);

// Same merchant again — expect error
[$p, $r, $ok3, $err3] = httpCall($http, 'POST', config('feenicia.endpoints.token_register'), $registerPayload);
$code3    = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isDupOk3 = !$ok3 || ($code3 !== '00');
$dt3 = new DateTime();
$results[] = [
    'name'             => "Register Merchant — same",
    'description'      => 'MERCHANT REGISTER',
    'execComments'     => "Generate the same Merchant's registration",
    'date'             => $dt3->format('d/m/Y'),
    'hour'             => $dt3->format('H:i:s'),
    'descriptionResult'=> 'It will not let the merchant to register',
    'result'           => $isDupOk3 ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $err3,
];
echo sprintf("  [%s] %-45s → %s\n", $isDupOk3 ? '✓' : '✗', "Register Merchant — same (expect error)", $isDupOk3 ? 'SUCCESS' : 'FAIL');

// Different merchant (fake merchant number) — expect success (allow)
$diffMerchantPayload = [
    'merchant'    => '0000000000099999',
    'affiliation' => $affiliation,
];
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_register'), $diffMerchantPayload);
record("Register Merchant — different merchant", 'MERCHANT REGISTER', "Feenicia's commerce register with a different Merchant than the original delivered.", 'It will allow the registration', $p, $r, $ok, $err, $results);

// ════════════════════════════════════════════════════════════════════════════
// SALE WITH STORE — Venta con token (TokenStore)
// Endpoint: POST /balder/token/saleToken
// ════════════════════════════════════════════════════════════════════════════
echo "\n[ SALE WITH STORE ]\n";

$saleTxDate     = (int) (microtime(true) * 1000);
$salePayload    = [
    'token'           => $cardToken,
    'amount'          => 1.00,
    'affiliation'     => $affiliation,
    'transactionDate' => $saleTxDate,
    'cvv2'            => $crypto->encrypt('123'),
    'tip'             => 0,
];

// 1. Sale with TokenStore + Email (sale + create receipt + send receipt)
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_sale'), $salePayload);
if ($ok) {
    $saleTokenTxId = $r['transactionId'] ?? null;
    $saleAuthnum   = $r['authnum']       ?? $r['authNum'] ?? null;
    $saleOrderId   = $r['orderId']       ?? null;
}
record('Sale with Store + Email', 'GENERATE RECEIPTS OF SALE', 'Generates a new Sale with TokenStore and Email', 'Generates the new Sale where the receipt will be sent by Email', $p, $r, $ok, $err, $results);
echo "  transactionId: {$saleTokenTxId}, authnum: {$saleAuthnum}\n";

// Send receipt by email if sale succeeded
if ($ok && ($r['ReciboId'] ?? $r['receiptId'] ?? null)) {
    $receiptId4 = $r['ReciboId'] ?? $r['receiptId'];
    httpCall($http, 'POST', config('feenicia.endpoints.send_receipt'), [
        'receiptId' => $receiptId4,
        'Email'     => [$crypto->encrypt('prueba@ejemplo.com')],
    ]);
}

// 2. Same sale again — expect error (duplicate)
[$p, $r, $ok5, $err5] = httpCall($http, 'POST', config('feenicia.endpoints.token_sale'), $salePayload);
$code5    = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isErrOk5 = !$ok5 || ($code5 !== '00');
$dt5 = new DateTime();
$results[] = [
    'name'             => 'Sale with Store + Email — same again',
    'description'      => 'GENERATE RECEIPTS OF SALE',
    'execComments'     => 'Generates again the same sale with TokenStore and Email',
    'date'             => $dt5->format('d/m/Y'),
    'hour'             => $dt5->format('H:i:s'),
    'descriptionResult'=> 'It will not allow to generate the same sale',
    'result'           => $isErrOk5 ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $err5,
];
echo sprintf("  [%s] %-45s → %s\n", $isErrOk5 ? '✓' : '✗', 'Same sale again (expect error)', $isErrOk5 ? 'SUCCESS' : 'FAIL');

// 3. Sale with Store without Email
$saleTxDate2  = (int) (microtime(true) * 1000);
$salePayload2 = array_merge($salePayload, ['transactionDate' => $saleTxDate2]);
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_sale'), $salePayload2);
record('Sale with Store — without Email', 'GENERATE RECEIPTS OF SALE', 'Generates a new sale with TokenStore and without Email', 'Generates the new Sale', $p, $r, $ok, $err, $results);

// 4. Sale with Email without Store (manual sale)
$saleTxDate3  = (int) (microtime(true) * 1000);
$manualPayload = [
    'affiliation'     => $affiliation,
    'amount'          => 1.00,
    'transactionDate' => $saleTxDate3,
    'pan'             => $crypto->encrypt('5439240350653004'),
    'cardholderName'  => $crypto->encrypt('PASAPORTE'),
    'cvv2'            => $crypto->encrypt('123'),
    'expDate'         => $crypto->encrypt('2701'),
    'userId'          => $userId,
    'tip'             => '0.0',
];
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.sale_one_step'), $manualPayload);
$manualTxId   = $r['transactionId'] ?? null;
$manualOrder  = $r['orderId']       ?? null;
$manualRecibo = $r['ReciboId']      ?? null;
record('Sale with Email — without Store', 'GENERATE RECEIPTS OF SALE', 'Generates a new sale with Email and without TokenStore', 'Generates the new Sale', $p, $r, $ok, $err, $results);
if ($ok && $manualRecibo) {
    httpCall($http, 'POST', config('feenicia.endpoints.send_receipt'), [
        'receiptId' => $manualRecibo,
        'Email'     => [$crypto->encrypt('prueba@ejemplo.com')],
    ]);
}

// 5. Sale without Store without Email (manual, no receipt)
$saleTxDate4   = (int) (microtime(true) * 1000);
$manualPayload2 = array_merge($manualPayload, ['transactionDate' => $saleTxDate4]);
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.sale_one_step'), $manualPayload2);
$reversalTxId   = $r['transactionId'] ?? null;
$reversalAuth   = $r['authnum']       ?? null;
$reversalOrder  = $r['orderId']       ?? null;
$reversalDate   = $saleTxDate4;
record('Sale without Store — without Email', 'GENERATE RECEIPTS OF SALE', 'Generates a new Sale without TokenStore and without Email', 'Generates the new Sale', $p, $r, $ok, $err, $results);

// 6. Sale with wrong Merchant-TokenStore (fake token)
$wrongSalePayload = array_merge($salePayload, [
    'token'           => 'invalidtoken00000000000000000000',
    'transactionDate' => (int) (microtime(true) * 1000),
]);
[$p, $r, $ok6, $err6] = httpCall($http, 'POST', config('feenicia.endpoints.token_sale'), $wrongSalePayload);
$code6   = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isErr6  = !$ok6 || ($code6 !== '00');
$dt6 = new DateTime();
$results[] = [
    'name'             => 'Sale with wrong Merchant-TokenStore',
    'description'      => 'GENERATE RECEIPTS OF SALE',
    'execComments'     => 'Generates a new Sale with an incorrect Merchant-TokenStore.',
    'date'             => $dt6->format('d/m/Y'),
    'hour'             => $dt6->format('H:i:s'),
    'descriptionResult'=> 'Generates the new Sale (expect error)',
    'result'           => $isErr6 ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $err6,
];
echo sprintf("  [%s] %-45s → %s\n", $isErr6 ? '✓' : '✗', 'Sale with wrong Merchant-TokenStore (expect error)', $isErr6 ? 'SUCCESS' : 'FAIL');

// 7-11. Timeout tests — N/A (require Feenicia QA timeout simulation endpoints)
foreach ([3, 5, 8, 15, 35] as $secs) {
    naStep(
        "Sale — Timeout: {$secs} seconds",
        'GENERATE RECEIPTS OF SALE',
        "Requires Feenicia QA timeout simulation endpoint. Cannot be triggered from client side.",
        $results
    );
}

// ════════════════════════════════════════════════════════════════════════════
// SALE CANCELLATION — Cancelación de venta con token
// Endpoint: POST /balder/token/cancelCard
// ════════════════════════════════════════════════════════════════════════════
echo "\n[ SALE CANCELLATION ]\n";

$cancelPayload = [
    'token'       => $cardToken,
    'affiliation' => $affiliation,
];

// 1. Wrong orderId (record error context in execComments)
$wrongOrderPayload = array_merge($cancelPayload, ['orderId' => 'WRONG_ORDER_999']);
[$p, $r, $ok7, $err7] = httpCall($http, 'POST', config('feenicia.endpoints.token_cancel'), $wrongOrderPayload);
$code7   = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isErr7  = !$ok7 || ($code7 !== '00');
$dt7 = new DateTime();
$results[] = [
    'name'             => 'Sale Cancellation — wrong orderId',
    'description'      => 'SALE CANCELLATION BY THE USER',
    'execComments'     => 'Sale cancellation - TokenStore with a different orderId',
    'date'             => $dt7->format('d/m/Y'),
    'hour'             => $dt7->format('H:i:s'),
    'descriptionResult'=> 'The sale cancellation will not exist because the data will not match',
    'result'           => $isErr7 ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $err7,
];
echo sprintf("  [%s] %-45s → %s\n", $isErr7 ? '✓' : '✗', 'Cancellation — wrong orderId (expect error)', $isErr7 ? 'SUCCESS' : 'FAIL');

// 2. Wrong transactionId (data mismatch)
$wrongTxPayload = array_merge($cancelPayload, ['transactionId' => '0000000']);
[$p, $r, $ok8, $err8] = httpCall($http, 'POST', config('feenicia.endpoints.token_cancel'), $wrongTxPayload);
$code8  = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isErr8 = !$ok8 || ($code8 !== '00');
$dt8 = new DateTime();
$results[] = [
    'name'             => 'Sale Cancellation — wrong transactionId (data mismatch)',
    'description'      => 'SALE CANCELLATION BY THE USER',
    'execComments'     => 'Sale cancellation - tokenStore with authnum and differents transactionId',
    'date'             => $dt8->format('d/m/Y'),
    'hour'             => $dt8->format('H:i:s'),
    'descriptionResult'=> 'The sale cancellation will not exist because the data will not match',
    'result'           => $isErr8 ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $err8,
];
echo sprintf("  [%s] %-45s → %s\n", $isErr8 ? '✓' : '✗', 'Cancellation — wrong txId (data mismatch)', $isErr8 ? 'SUCCESS' : 'FAIL');

// 3. Wrong transactionId (security PIN mismatch)
$wrongPinPayload = array_merge($cancelPayload, ['authnum' => '000000', 'transactionId' => '9999999']);
[$p, $r, $ok9, $err9] = httpCall($http, 'POST', config('feenicia.endpoints.token_cancel'), $wrongPinPayload);
$code9  = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isErr9 = !$ok9 || ($code9 !== '00');
$dt9 = new DateTime();
$results[] = [
    'name'             => 'Sale Cancellation — wrong PIN',
    'description'      => 'SALE CANCELLATION BY THE USER',
    'execComments'     => 'Sale cancellation - tokenStore with authnum and differents transactionId (PIN mismatch)',
    'date'             => $dt9->format('d/m/Y'),
    'hour'             => $dt9->format('H:i:s'),
    'descriptionResult'=> 'The sale cancellation will not exist because the security PIN will not match.',
    'result'           => $isErr9 ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $err9,
];
echo sprintf("  [%s] %-45s → %s\n", $isErr9 ? '✓' : '✗', 'Cancellation — wrong PIN (expect error)', $isErr9 ? 'SUCCESS' : 'FAIL');

// 4. Correct cancellation
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_cancel'), $cancelPayload);
record('Sale Cancellation — tokenStore', 'SALE CANCELLATION BY THE USER', 'Sale Cancellation - tokenStore', 'Cancel the sale', $p, $r, $ok, $err, $results);

// 5. Cancel same again — expect error
[$p, $r, $okA, $errA] = httpCall($http, 'POST', config('feenicia.endpoints.token_cancel'), $cancelPayload);
$codeA  = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isErrA = !$okA || ($codeA !== '00');
$dtA = new DateTime();
$results[] = [
    'name'             => 'Sale Cancellation — same again',
    'description'      => 'SALE CANCELLATION BY THE USER',
    'execComments'     => 'Cancel again the same sale - tokenStore',
    'date'             => $dtA->format('d/m/Y'),
    'hour'             => $dtA->format('H:i:s'),
    'descriptionResult'=> 'It will not let to cancel a sale previously canceled',
    'result'           => $isErrA ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $errA,
];
echo sprintf("  [%s] %-45s → %s\n", $isErrA ? '✓' : '✗', 'Cancel same again (expect error)', $isErrA ? 'SUCCESS' : 'FAIL');

// 6. Cancellation without tokenStore (manual cancellation using PostSaleData)
$manualCancelPayload = [
    'affiliation'     => $affiliation,
    'amount'          => 1.00,
    'transactionDate' => (int) (microtime(true) * 1000),
    'orderId'         => (string) ($reversalOrder ?? $saleOrderId ?? '0'),
    'pan'             => $crypto->encrypt('5439240350653004'),
    'cardholderName'  => $crypto->encrypt('PASAPORTE'),
    'expDate'         => $crypto->encrypt('2701'),
    'authnum'         => (string) ($reversalAuth  ?? $saleAuthnum  ?? '000000'),
    'transactionId'   => (string) ($reversalTxId  ?? $saleTokenTxId ?? '0'),
];
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.cancellation'), $manualCancelPayload);
record('Sale Cancellation — without tokenStore', 'SALE CANCELLATION BY THE USER', 'Sale cancellation - without tokenStore', 'Cancel the sale', $p, $r, $ok, $err, $results);

// ════════════════════════════════════════════════════════════════════════════
// REVERSE BY DEMAND — Reverso de venta
// Endpoint: POST /balder/token/reversalSale
// ════════════════════════════════════════════════════════════════════════════
echo "\n[ REVERSE BY DEMAND ]\n";

// Use the "Sale without Store — without Email" transaction for reversal
$reversalPayload = [
    'token'           => $cardToken,
    'affiliation'     => $affiliation,
    'amount'          => 1.00,
    'transactionDate' => (int) (microtime(true) * 1000),
    'transactionId'   => (string) ($reversalTxId ?? $saleTokenTxId ?? '0'),
    'authnum'         => (string) ($reversalAuth ?? $saleAuthnum ?? '000000'),
];

[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_reversal'), $reversalPayload);
record('Reverse by demand — Invalidate Sale', 'REVERSE SALE', 'Invalidate Sale', 'It will allow to invalidate a sale', $p, $r, $ok, $err, $results);

// Invalidate already-cancelled sale — expect error
[$p, $r, $okB, $errB] = httpCall($http, 'POST', config('feenicia.endpoints.token_reversal'), $reversalPayload);
$codeB  = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isErrB = !$okB || ($codeB !== '00');
$dtB = new DateTime();
$results[] = [
    'name'             => 'Reverse — already cancelled',
    'description'      => 'REVERSE SALE',
    'execComments'     => 'Invalidate a sale previously canceled',
    'date'             => $dtB->format('d/m/Y'),
    'hour'             => $dtB->format('H:i:s'),
    'descriptionResult'=> 'It will not allow to invalidate a sale previously canceled',
    'result'           => $isErrB ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $errB,
];
echo sprintf("  [%s] %-45s → %s\n", $isErrB ? '✓' : '✗', 'Reverse already-cancelled (expect error)', $isErrB ? 'SUCCESS' : 'FAIL');

// ════════════════════════════════════════════════════════════════════════════
// CARD UPDATE — Actualizar datos de tarjeta
// Endpoint: POST /balder/token/updateCard
// ════════════════════════════════════════════════════════════════════════════
echo "\n[ CARD UPDATE ]\n";

$updatePayload = [
    'token'       => $cardToken,
    'affiliation' => $affiliation,
    'expDate'     => $crypto->encrypt('2712'),
    'alias'       => 'Tarjeta QA actualizada',
];
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_update'), $updatePayload);
record('Card Update', 'CARDHOLDER DATA UPDATE', 'Cardholder data update', 'Data Update', $p, $r, $ok, $err, $results);

// ════════════════════════════════════════════════════════════════════════════
// CARD RECOVERY — Obtener tarjetas del merchant
// Endpoint: GET /balder/token/getCards
// ════════════════════════════════════════════════════════════════════════════
echo "\n[ CARD RECOVERY ]\n";

[$p, $r, $ok, $err] = httpCall($http, 'GET', config('feenicia.endpoints.token_get_cards'), ['affiliation' => $affiliation]);
record('Card Recovery', 'CARDHOLDER DATA BALDER REGISTER RECOVERY', 'Cardholder data recovery', 'You must see Cardholder Data', $p, $r, $ok, $err, $results);

// ════════════════════════════════════════════════════════════════════════════
// DELETE CARD — Eliminar tarjeta
// Endpoint: POST /balder/token/deleteCard
// ════════════════════════════════════════════════════════════════════════════
echo "\n[ DELETE CARD ]\n";

$deletePayload = [
    'token'       => $cardToken,
    'affiliation' => $affiliation,
];

// Happy path
[$p, $r, $ok, $err] = httpCall($http, 'POST', config('feenicia.endpoints.token_delete'), $deletePayload);
record('Delete Card', 'DELETE THE CARD FROM THE USER ACCOUNT', 'Cardholder data erase', 'The Balder user card should be erased', $p, $r, $ok, $err, $results);

// Delete same card again — expect error
[$p, $r, $okC, $errC] = httpCall($http, 'POST', config('feenicia.endpoints.token_delete'), $deletePayload);
$codeC  = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isErrC = !$okC || ($codeC !== '00');
$dtC = new DateTime();
$results[] = [
    'name'             => 'Delete Card — same again',
    'description'      => 'DELETE THE CARD FROM THE USER ACCOUNT',
    'execComments'     => 'Delete again the same card',
    'date'             => $dtC->format('d/m/Y'),
    'hour'             => $dtC->format('H:i:s'),
    'descriptionResult'=> 'It will not let to delete the same card',
    'result'           => $isErrC ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $errC,
];
echo sprintf("  [%s] %-45s → %s\n", $isErrC ? '✓' : '✗', 'Delete Card same again (expect error)', $isErrC ? 'SUCCESS' : 'FAIL');

// Sale with deleted card — expect error
$deletedSalePayload = array_merge($salePayload, [
    'transactionDate' => (int) (microtime(true) * 1000),
]);
[$p, $r, $okD, $errD] = httpCall($http, 'POST', config('feenicia.endpoints.token_sale'), $deletedSalePayload);
$codeD  = $r['responseCode'] ?? $r['ResponseCode'] ?? null;
$isErrD = !$okD || ($codeD !== '00');
$dtD = new DateTime();
$results[] = [
    'name'             => 'Sale with deleted card',
    'description'      => 'DELETE THE CARD FROM THE USER ACCOUNT',
    'execComments'     => 'Generates a sale with an erased card',
    'date'             => $dtD->format('d/m/Y'),
    'hour'             => $dtD->format('H:i:s'),
    'descriptionResult'=> 'It will not do any sale because the card does not exist.',
    'result'           => $isErrD ? 'SUCCESS' : 'FAIL',
    'request'          => $p,
    'response'         => $r,
    'error'            => $errD,
];
echo sprintf("  [%s] %-45s → %s\n", $isErrD ? '✓' : '✗', 'Sale with deleted card (expect error)', $isErrD ? 'SUCCESS' : 'FAIL');

// ════════════════════════════════════════════════════════════════════════════
// Guardar resultados
// ════════════════════════════════════════════════════════════════════════════
$outputPath = __DIR__ . '/feenicia_balder_results.json';
file_put_contents($outputPath, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$success = count(array_filter($results, fn($r) => $r['result'] === 'SUCCESS'));
$na      = count(array_filter($results, fn($r) => $r['result'] === 'N/A'));
$total   = count($results);

echo "\n=== Resumen ===\n";
echo "{$success}/" . ($total - $na) . " pasos exitosos ({$na} N/A de timeouts)\n";
echo "Resultados: {$outputPath}\n";
