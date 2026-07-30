<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Entorno
    |--------------------------------------------------------------------------
    | 'test' o 'production'. Controla qué host de OPPWA se usa.
    */
    'env' => env('PROSA_ENV', 'test'),

    /*
    |--------------------------------------------------------------------------
    | Hosts OPPWA / ACI
    |--------------------------------------------------------------------------
    | El ProsaHttpClient resuelve el host según 'env'.
    */
    'hosts' => [
        'test' => env('PROSA_HOST_TEST', 'https://eu-test.oppwa.com'),
        'production' => env('PROSA_HOST_PROD', 'https://eu-prod.oppwa.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Credenciales del comercio
    |--------------------------------------------------------------------------
    | entity_id:    Identificador de canal/comercio (AN32). Es el valor que
    |               aparece en "Entity ID(s)" en el documento de credenciales
    |               Prosa (o después del segundo = en OPP VT Access URL).
    | access_token: Token Bearer (Administration > Account data). Es el campo
    |               "ACCESS TOKEN" en el documento de credenciales.
    | sender_id:    Entity ID adicional del "Sender/canal de tarjetas". Se usa
    |               en operaciones de tokenización (registros). Provisto en el
    |               campo "SENDER" del documento de credenciales.
    */
    'entity_id' => env('PROSA_ENTITY_ID'),
    'access_token' => env('PROSA_ACCESS_TOKEN'),
    'sender_id' => env('PROSA_SENDER_ID'),

    /*
    |--------------------------------------------------------------------------
    | Moneda
    |--------------------------------------------------------------------------
    */
    'currency' => env('PROSA_CURRENCY', 'MXN'),

    /*
    |--------------------------------------------------------------------------
    | Descriptor (número de afiliación bancaria)
    |--------------------------------------------------------------------------
    | Provisto por el banco. Se envía como `descriptor` en cada transacción.
    */
    'descriptor' => env('PROSA_DESCRIPTOR', '7639599'),

    /*
    |--------------------------------------------------------------------------
    | testMode
    |--------------------------------------------------------------------------
    | Sólo se envía cuando env = 'test'. El banco exige 'EXTERNAL' en pruebas.
    */
    'test_mode' => env('PROSA_TEST_MODE', 'EXTERNAL'),

    /*
    |--------------------------------------------------------------------------
    | 3-D Secure 2
    |--------------------------------------------------------------------------
    | challenge_window: tamaño de la ventana de reto del ACS.
    |   01=250x400 02=390x400 03=500x600 04=600x400 05=pantalla completa
    | country: país por defecto (ISO 3166-1 alpha-2) para billing.country.
    */
    'three_ds' => [
        'enabled' => (bool) env('PROSA_3DS_ENABLED', true),
        'challenge_window' => env('PROSA_3DS_CHALLENGE_WINDOW', '05'),
        'country' => env('PROSA_3DS_COUNTRY', 'MX'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Datos del comercio (merchant.*) para el Checkout
    |--------------------------------------------------------------------------
    | Parámetros estáticos del comercio exigidos por Prosa en el "Checkout".
    | No son datos del tarjetahabiente: describen al comercio y se envían en
    | cada transacción. Ajusta los valores a la dirección real del comercio.
    |
    | Formatos validados por Prosa:
    |   url       AN255  URL del comercio (cualquier carácter, máx. 255).
    |   city      A13    Sólo letras, máx. 13 (ej. CDMX, Monterrey).
    |   state     A3     ISO 3166-2:MX sin prefijo (ej. CMX, JAL, NLE).
    |   country   A3     ISO 3166-1 alpha-3 (ej. MEX).
    |   postcode  AN10   Código postal alfanumérico, máx. 10.
    */
    'merchant' => [
        'url' => env('PROSA_MERCHANT_URL'),
        'shipping' => [
            'city' => env('PROSA_MERCHANT_SHIPPING_CITY', 'CDMX'),
            'state' => env('PROSA_MERCHANT_SHIPPING_STATE', 'CMX'),
            'country' => env('PROSA_MERCHANT_SHIPPING_COUNTRY', 'MEX'),
            'postcode' => env('PROSA_MERCHANT_SHIPPING_POSTCODE', '06000'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook
    |--------------------------------------------------------------------------
    | OPPWA cifra las notificaciones con AES-256-GCM. La llave y el IV
    | (hex) se obtienen en el backend OPPWA bajo la configuración del canal.
    */
    'webhook' => [
        'secret' => env('PROSA_WEBHOOK_SECRET'),
        'iv' => env('PROSA_WEBHOOK_IV'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    */
    'http' => [
        'timeout' => 30,    // segundos
        'connect_timeout' => 10,
        'retry_times' => 1,     // 1 reintento en timeout
        'retry_sleep_ms' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Endpoints (relativos al host)
    |--------------------------------------------------------------------------
    */
    'endpoints' => [
        'payments' => '/v1/payments',
        'payment' => '/v1/payments/%s',          // GET status / POST backoffice
        'registrations' => '/v1/registrations',
        'registration' => '/v1/registrations/%s',     // DELETE
        'reg_payments' => '/v1/registrations/%s/payments',
    ],

];
