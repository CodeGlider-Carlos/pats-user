<?php

namespace App\Services\Feenicia;

use RuntimeException;

/**
 * Genera el header de firma x-requested-with para cada request a Feenicia.
 *
 * Basado en el SDK oficial PHP de Feenicia (github.com/yorch81/feenicia).
 *
 * Proceso exacto del SDK:
 *  1. SHA256 del JSON → string HEX (64 chars)
 *  2. PKCS7 padding manual al string HEX (bloques de 16 bytes)
 *  3. AES-128-CBC (Rijndael 128) del string paddeado con SignatureKey + SignatureIV
 *  4. bin2hex del resultado → accessToken
 *  5. Concatenar: {merchant}_{accessToken}
 */
class FeeniciaSignatureService
{
    private const CIPHER   = 'AES-128-CBC'; // Rijndael 128 = AES-128
    private const KEY_SIZE = 16;            // AES-128 → llave de 16 bytes
    private const IV_SIZE  = 16;            // CBC → IV de 16 bytes
    private const BLOCK    = 16;            // Tamaño de bloque para PKCS7

    public function __construct(
        private readonly string $signatureKey,
        private readonly string $signatureIv,
        private readonly string $merchant,
    ) {
        $this->validateKeys();
    }

    /**
     * Genera el valor completo del header x-requested-with.
     *
     * @param  array<string, mixed> $payload
     * @return string               merchant_accessToken
     */
    public function buildHeader(array $payload): string
    {
        $json        = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $accessToken = $this->generateAccessToken($json);
        $header      = "{$this->merchant}_{$accessToken}";

        \Log::channel('feenicia')->info('Header generado', [
            'merchant'     => $this->merchant,
            'json_firmado' => $json,
        ]);

        return $header;
    }

    /**
     * Genera el accessToken replicando exactamente el SDK de Feenicia.
     *
     * @param  string $json  Body del request como JSON string
     * @return string        accessToken en HEX
     */
    public function generateAccessToken(string $json): string
    {
        // Paso 1: SHA256 del JSON como string HEX (64 chars)
        $sha256Hex = hash('sha256', $json);

        // Paso 2: PKCS7 padding manual (igual que el SDK PHP original)
        $pad       = self::BLOCK - (strlen($sha256Hex) % self::BLOCK);
        $sha256Hex .= str_repeat(chr($pad), $pad);

        // Paso 3: AES-128-CBC con las llaves de firma
        $keyBytes = $this->prepareKey($this->signatureKey, self::KEY_SIZE);
        $ivBytes  = $this->prepareKey($this->signatureIv,  self::IV_SIZE);

        $encrypted = openssl_encrypt(
            data:        $sha256Hex,
            cipher_algo: self::CIPHER,
            passphrase:  $keyBytes,
            options:     OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, // sin padding automático
            iv:          $ivBytes,
        );

        if ($encrypted === false) {
            throw new RuntimeException(
                'Feenicia: error al generar accessToken. ' . openssl_error_string()
            );
        }

        // Paso 4: bin2hex del resultado
        return bin2hex($encrypted);
    }

    // ──────────────────────────────────────────────
    //  Internos
    // ──────────────────────────────────────────────

    /**
     * Prepara la llave al tamaño exacto requerido.
     * AES-128 usa llaves de 16 bytes (a diferencia de AES-256 que usa 32).
     */
    private function prepareKey(string $raw, int $size): string
    {
        // HEX de exactamente (size * 2) chars → decodificar
        if (strlen($raw) === $size * 2 && ctype_xdigit($raw)) {
            return hex2bin($raw);
        }

        // Cualquier otro formato → truncar o paddear al tamaño exacto
        return str_pad(substr($raw, 0, $size), $size, "\0");
    }

    private function validateKeys(): void
    {
        if (empty($this->signatureKey) || empty($this->signatureIv)) {
            throw new RuntimeException(
                'Feenicia: FEENICIA_SIGNATURE_KEY y FEENICIA_SIGNATURE_IV son obligatorios en .env'
            );
        }

        if (empty($this->merchant)) {
            throw new RuntimeException(
                'Feenicia: FEENICIA_MERCHANT es obligatorio en .env'
            );
        }
    }
}