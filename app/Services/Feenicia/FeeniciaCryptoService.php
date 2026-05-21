<?php

namespace App\Services\Feenicia;

use RuntimeException;

/**
 * Encripta y desencripta campos sensibles usando AES-128-CBC.
 *
 * Basado en el SDK oficial PHP de Feenicia (github.com/yorch81/feenicia).
 *
 * El SDK usa mcrypt_encrypt(MCRYPT_RIJNDAEL_128, ...) que equivale a AES-128-CBC.
 * Las llaves son de 16 bytes (hex2bin de 32 chars HEX).
 * El padding es PKCS7 manual.
 */
class FeeniciaCryptoService
{
    private const CIPHER   = 'AES-128-CBC'; // Rijndael 128 = AES-128
    private const KEY_SIZE = 16;            // 16 bytes = 32 chars HEX
    private const IV_SIZE  = 16;
    private const BLOCK    = 16;

    public function __construct(
        private readonly string $key,
        private readonly string $iv,
    ) {
        $this->validateKeys();
    }

    /**
     * Encripta un campo sensible y devuelve el resultado en HEX.
     * Replica exactamente cryptData() del SDK de Feenicia.
     */
    public function encrypt(string $plainText): string
    {
        $keyBytes = $this->prepareKey($this->key, self::KEY_SIZE);
        $ivBytes  = $this->prepareKey($this->iv,  self::IV_SIZE);

        // PKCS7 padding manual (igual que el SDK)
        $pad       = self::BLOCK - (strlen($plainText) % self::BLOCK);
        $plainText .= str_repeat(chr($pad), $pad);

        // AES-128-CBC sin padding automático de OpenSSL
        $encrypted = openssl_encrypt(
            data:        $plainText,
            cipher_algo: self::CIPHER,
            passphrase:  $keyBytes,
            options:     OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            iv:          $ivBytes,
        );

        if ($encrypted === false) {
            throw new RuntimeException(
                'Feenicia: error al encriptar. ' . openssl_error_string()
            );
        }

        return bin2hex($encrypted);
    }

    /**
     * Desencripta un campo que viene en HEX desde Feenicia.
     */
    public function decrypt(string $hexCipherText): string
    {
        $keyBytes    = $this->prepareKey($this->key, self::KEY_SIZE);
        $ivBytes     = $this->prepareKey($this->iv,  self::IV_SIZE);
        $cipherBytes = hex2bin($hexCipherText);

        if ($cipherBytes === false) {
            throw new RuntimeException('Feenicia: cipherText no es HEX válido.');
        }

        $decrypted = openssl_decrypt(
            data:        $cipherBytes,
            cipher_algo: self::CIPHER,
            passphrase:  $keyBytes,
            options:     OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            iv:          $ivBytes,
        );

        if ($decrypted === false) {
            throw new RuntimeException(
                'Feenicia: error al desencriptar. ' . openssl_error_string()
            );
        }

        // Quitar PKCS7 padding
        $pad = ord($decrypted[strlen($decrypted) - 1]);
        return substr($decrypted, 0, strlen($decrypted) - $pad);
    }

    /**
     * Encripta múltiples campos de un array de una vez.
     *
     * @param  array<string, mixed> $data
     * @param  string[]             $fields
     * @return array<string, mixed>
     */
    public function encryptFields(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $data[$field] = $this->encrypt((string) $data[$field]);
            }
        }

        return $data;
    }

    // ──────────────────────────────────────────────
    //  Internos
    // ──────────────────────────────────────────────

    /**
     * Prepara la llave al tamaño exacto.
     * AES-128 usa 16 bytes = 32 chars HEX.
     */
    private function prepareKey(string $raw, int $size): string
    {
        // HEX de exactamente (size * 2) chars → decodificar a bytes
        if (strlen($raw) === $size * 2 && ctype_xdigit($raw)) {
            return hex2bin($raw);
        }

        // Cualquier otro formato → truncar o paddear
        return str_pad(substr($raw, 0, $size), $size, "\0");
    }

    private function validateKeys(): void
    {
        if (empty($this->key) || empty($this->iv)) {
            throw new RuntimeException(
                'Feenicia: FEENICIA_REQUEST_KEY y FEENICIA_REQUEST_IV son obligatorios en .env'
            );
        }
    }
}