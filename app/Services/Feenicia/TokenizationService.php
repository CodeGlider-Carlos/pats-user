<?php

namespace App\Services\Feenicia;

use App\DTO\Feenicia\Tokenization\TokenSignupData;
use App\DTO\Feenicia\Tokenization\GenerateTokenData;
use App\DTO\Feenicia\Tokenization\SaleTokenData;
use App\DTO\Feenicia\Tokenization\CancelCardData;
use App\DTO\Feenicia\Tokenization\UpdateCardData;
use App\DTO\Feenicia\Tokenization\DeleteCardData;
use App\DTO\Feenicia\Tokenization\TokenReversalData;
use App\DTO\Feenicia\Tokenization\TokenRefundData;
use App\Exceptions\Feenicia\FeeniciaException;
use App\Models\FeeniciaToken;
use App\Models\FeeniciaTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Gestiona la tokenización de tarjetas mediante el módulo Balder de Feenicia.
 *
 * Flujo inicial (solo una vez por merchant):
 *  1. signup()     → registra el merchant en Balder
 *  2. getKey()     → obtiene la llave pública de Balder
 *  3. register()   → asocia el merchant de Feenicia con Balder
 *
 * Flujo por tarjeta:
 *  1. generateToken()  → tokeniza la tarjeta, devuelve un token
 *  2. saleToken()      → cobra usando el token (sin PAN)
 *  3. cancelCard() / updateCard() / deleteCard() → gestión
 *
 * Uso:
 *   // Tokenizar tarjeta nueva
 *   $tokenData = $service->generateToken($data, $userId);
 *   // $tokenData['token'] → guardar en FeeniciaToken
 *
 *   // Cobrar con tarjeta guardada
 *   $result = $service->saleToken($saleData);
 */
class TokenizationService
{
    // Tiempo de caché de la llave de Balder (24 horas)
    private const KEY_CACHE_TTL = 86400;

    public function __construct(
        private readonly FeeniciaHttpClient    $http,
        private readonly FeeniciaCryptoService $crypto,
    ) {}

    // ──────────────────────────────────────────────
    //  Setup inicial del merchant (ejecutar una vez)
    // ──────────────────────────────────────────────

    /**
     * Registra el merchant en Balder.
     * Solo necesita ejecutarse una vez durante la configuración inicial.
     */
    public function signup(TokenSignupData $data): array
    {
        return $this->http->post(
            config('feenicia.endpoints.token_signup'),
            $data->toArray()
        );
    }

    /**
     * Obtiene la llave pública de Balder.
     * Se cachea por 24 horas para no llamar en cada request.
     */
    public function getKey(): string
    {
        return Cache::remember('feenicia.balder.key', self::KEY_CACHE_TTL, function () {
            $result = $this->http->get(config('feenicia.endpoints.token_get_key'));
            return $result['key'] ?? $result['publicKey'] ?? '';
        });
    }

    /**
     * Asocia el merchant de Feenicia con Balder.
     * Solo necesita ejecutarse una vez.
     */
    public function registerMerchant(): array
    {
        return $this->http->post(
            config('feenicia.endpoints.token_register'),
            [
                'merchant'    => config('feenicia.merchant'),
                'affiliation' => config('feenicia.affiliation'),
            ]
        );
    }

    // ──────────────────────────────────────────────
    //  Tokenización de tarjeta
    // ──────────────────────────────────────────────

    /**
     * Tokeniza una tarjeta y la guarda en la BD.
     *
     * @param  GenerateTokenData $data    Datos de la tarjeta
     * @param  int               $userId  ID del usuario en tu sistema
     * @param  bool              $asDefault  Marcar como tarjeta default
     *
     * @return FeeniciaToken  Registro creado en BD
     *
     * @throws FeeniciaException
     */
    public function generateToken(
        GenerateTokenData $data,
        int               $userId,
        bool              $asDefault = false,
    ): FeeniciaToken {
        // Encriptar campos sensibles
        $payload = $this->crypto->encryptFields(
            $data->toArray(),
            GenerateTokenData::encryptedFields()
        );

        $result = $this->http->post(
            config('feenicia.endpoints.token_generate'),
            $payload
        );

        Log::channel('feenicia')->info('Token generado', [
            'userId' => $userId,
            'brand'  => $result['card']['brand'] ?? null,
            'last4'  => $result['card']['last4Digits'] ?? null,
        ]);

        // Guardar en BD
        $token = FeeniciaToken::create([
            'user_id'         => $userId,
            'token'           => $result['token'],
            'alias'           => $data->alias,
            'affiliation'     => $data->affiliation,
            'card_brand'      => $result['card']['brand'] ?? null,
            'card_product'    => $result['card']['product'] ?? null,
            'card_last4'      => $result['card']['last4Digits'] ?? null,
            'card_first6'     => $result['card']['first6Digits'] ?? null,
            'cardholder_name' => $data->cardholderName,
            'exp_date'        => $data->expDate,
            'status'          => 'active',
            'is_default'      => false,
        ]);

        if ($asDefault) {
            $token->setAsDefault();
        }

        return $token;
    }

    // ──────────────────────────────────────────────
    //  Cobro con token
    // ──────────────────────────────────────────────

    /**
     * Ejecuta una venta usando un token de tarjeta guardado.
     * No requiere PAN — solo el token y el CVV.
     *
     * @param  SaleTokenData            $data
     * @return array<string, mixed>     Respuesta completa de Feenicia
     *
     * @throws FeeniciaException
     */
    public function saleToken(SaleTokenData $data): array
    {
        $payload = $this->crypto->encryptFields(
            $data->toArray(),
            SaleTokenData::encryptedFields()
        );

        $result = $this->http->post(
            config('feenicia.endpoints.token_sale'),
            $payload
        );

        Log::channel('feenicia')->info('Venta con token', [
            'transactionId' => $result['transactionId'] ?? null,
            'approved'      => $result['approved'] ?? null,
        ]);

        return $result;
    }

    // ──────────────────────────────────────────────
    //  Gestión de tarjetas
    // ──────────────────────────────────────────────

    /**
     * Cancela una tarjeta tokenizada en Balder y actualiza la BD.
     */
    public function cancelCard(CancelCardData $data): array
    {
        $result = $this->http->post(
            config('feenicia.endpoints.token_cancel'),
            $data->toArray()
        );

        // Actualizar estado en BD
        FeeniciaToken::where('token', $data->token)
                     ->update(['status' => 'cancelled']);

        return $result;
    }

    /**
     * Actualiza los datos de una tarjeta tokenizada.
     */
    public function updateCard(UpdateCardData $data): array
    {
        $payload = $this->crypto->encryptFields(
            $data->toArray(),
            UpdateCardData::encryptedFields()
        );

        $result = $this->http->post(
            config('feenicia.endpoints.token_update'),
            $payload
        );

        // Actualizar en BD si cambió la fecha de expiración
        if ($data->expDate || $data->alias) {
            FeeniciaToken::where('token', $data->token)->update(
                array_filter([
                    'exp_date' => $data->expDate,
                    'alias'    => $data->alias,
                ])
            );
        }

        return $result;
    }

    /**
     * Elimina permanentemente una tarjeta tokenizada.
     */
    public function deleteCard(DeleteCardData $data): array
    {
        $result = $this->http->post(
            config('feenicia.endpoints.token_delete'),
            $data->toArray()
        );

        FeeniciaToken::where('token', $data->token)->delete();

        return $result;
    }

    /**
     * Obtiene todas las tarjetas tokenizadas de un merchant.
     */
    public function getCards(string $affiliation): array
    {
        return $this->http->get(
            config('feenicia.endpoints.token_get_cards'),
            ['affiliation' => $affiliation]
        );
    }

    // ──────────────────────────────────────────────
    //  Post-venta con token
    // ──────────────────────────────────────────────

    /**
     * Reverso de una venta con token.
     */
    public function reversalSale(TokenReversalData $data): array
    {
        return $this->http->post(
            config('feenicia.endpoints.token_reversal'),
            $data->toArray()
        );
    }

    /**
     * Reembolso de una venta con token.
     */
    public function refundTx(TokenRefundData $data): array
    {
        return $this->http->post(
            config('feenicia.endpoints.token_refund'),
            $data->toArray()
        );
    }
}
