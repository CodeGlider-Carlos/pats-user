<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Entorno
    |--------------------------------------------------------------------------
    | 'qa' o 'production'. Controla qué base_url se usa.
    */
    'env' => env('FEENICIA_ENV', 'qa'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    */
    'base_url' => env('FEENICIA_BASE_URL', 'https://feenicia.net'),

    /*
    |--------------------------------------------------------------------------
    | Credenciales del merchant
    |--------------------------------------------------------------------------
    */
    'merchant'    => env('FEENICIA_MERCHANT'),
    'user'        => env('FEENICIA_USER'),
    'affiliation' => env('FEENICIA_AFFILIATION'),

    /*
    |--------------------------------------------------------------------------
    | Llaves de encriptación AES-256
    |--------------------------------------------------------------------------
    | Todas deben ser de 32 caracteres hexadecimales.
    | Provistas por SERTI/Feenicia para cada entorno.
    */
    'keys' => [

        // Encriptación de campos individuales (pan, cardholderName, cvv2, expDate, email)
        'request' => [
            'key' => env('FEENICIA_REQUEST_KEY'),
            'iv'  => env('FEENICIA_REQUEST_IV'),
        ],

        // Firma del header x-requested-with (SHA256 del JSON → AES256)
        'signature' => [
            'key' => env('FEENICIA_SIGNATURE_KEY'),
            'iv'  => env('FEENICIA_SIGNATURE_IV'),
        ],

        // Desencriptación de respuestas (si aplica)
        'response' => [
            'key' => env('FEENICIA_RESPONSE_KEY'),
            'iv'  => env('FEENICIA_RESPONSE_IV'),
        ],

        // Firma de respuestas
        'response_signature' => [
            'key' => env('FEENICIA_RESPONSE_SIGNATURE_KEY'),
            'iv'  => env('FEENICIA_RESPONSE_SIGNATURE_IV'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook
    |--------------------------------------------------------------------------
    | Secret JWT provisto por SERTI. Diferente en QA y Producción.
    */
    'webhook' => [
        'secret'    => env('FEENICIA_WEBHOOK_SECRET'),
        'algorithm' => 'HS384',
    ],

    /*
    |--------------------------------------------------------------------------
    | Endpoints
    |--------------------------------------------------------------------------
    | Paths relativos a base_url.
    | El FeeniciaHttpClient concatena base_url + el path correspondiente.
    */
    'endpoints' => [

        // ── Orden y firma (compartidos por todos los flujos de 5 pasos) ──
        'create_order'    => '/receipt/order/create',
        'save_signature'  => '/receipt/signature/save',
        'create_receipt'  => '/receipt/receipt/CreateReceipt',
        'send_receipt'    => '/receipt/receipt/SendReceipt',
        'get_receipt'     => '/receipt/receipt/GetReceiptFile',
        'customer_info'   => '/receipt/update/customerinfo',

        // ── Ventas ──
        'sale_manual'     => '/v1/atna/sale/manual',
        'sale_one_step'   => '/v1/atna/sale/oneStepSaleManual',
        'sale_emv'        => '/v1/atna/sale/emv',
        'sale_swipe'      => '/v1/atna/sale/card',
        'sale_cash'       => '/v1/atna/sale/cash',
        'sale_recurring'  => '/v1/atna/sale/recurringCharge',

        // ── Post-venta ──
        'reversal'        => '/v1/atna/reversal',
        'cancellation'    => '/v1/atna/cancel/manual',
        'refund'          => '/v1/atna/refund',

        // ── Tokenización (módulo Balder) ──
        'token_signup'          => '/balder/auth/signup',
        'token_get_key'         => '/balder/auth/getKey',
        'token_generate'        => '/balder/token/generateToken',
        'token_register'        => '/balder/auth/registerMerchantFeenicia',
        'token_sale'            => '/balder/token/saleToken',
        'token_cancel'          => '/balder/token/cancelCard',
        'token_reversal'        => '/balder/token/reversalSale',
        'token_update'          => '/balder/token/updateCard',
        'token_delete'          => '/balder/token/deleteCard',
        'token_get_cards'       => '/balder/token/getCards',
        'token_refund'          => '/balder/token/refundTx',
        'token_recurring'       => '/balder/sale/recurringCharge',

        // ── Reportes ──
        'report_period_year'  => '/dashboard/Transactions/PeriodYear',
        'report_period_month' => '/dashboard/Transactions/PeriodMonth',
        'report_period'       => '/dashboard/Transactions/Period',
        'report_banks'        => '/dashboard/Transactions/Banks',
        'report_marks'        => '/dashboard/Transactions/Marks',
        'report_transactions' => '/dashboard/Transactions/Transactions',
        'report_search'       => '/dashboard/Transactions/Search',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    */
    'http' => [
        'timeout'         => 30,    // segundos
        'connect_timeout' => 10,
        'retry_times'     => 1,     // 1 reintento en timeout (antes de enviar reversal)
        'retry_sleep_ms'  => 500,
    ],

];