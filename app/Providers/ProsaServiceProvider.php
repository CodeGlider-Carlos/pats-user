<?php

namespace App\Providers;

use App\DTO\Prosa\MerchantData;
use App\Services\Prosa\BackofficeService;
use App\Services\Prosa\Checkout\AdquirirCheckout;
use App\Services\Prosa\Checkout\CheckoutManager;
use App\Services\Prosa\Checkout\PagosCheckout;
use App\Services\Prosa\Checkout\PagosRecurringCheckout;
use App\Services\Prosa\Checkout\PagosRenovacionCheckout;
use App\Services\Prosa\Checkout\SolicitudCheckout;
use App\Services\Prosa\OxxoService;
use App\Services\Prosa\PaymentService;
use App\Services\Prosa\ProsaHttpClient;
use App\Services\Prosa\RecurringService;
use App\Services\Prosa\TokenizationService;
use Illuminate\Support\ServiceProvider;

class ProsaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProsaHttpClient::class, function () {
            $env = (string) config('prosa.env');

            return new ProsaHttpClient(
                baseUrl: rtrim(config('prosa.hosts.'.$env), '/'),
                accessToken: (string) config('prosa.access_token'),
                entityId: (string) config('prosa.entity_id'),
                descriptor: (string) config('prosa.descriptor'),
                testMode: $env === 'test' ? (string) config('prosa.test_mode') : null,
                timeout: (int) config('prosa.http.timeout'),
                connectTimeout: (int) config('prosa.http.connect_timeout'),
                retryTimes: (int) config('prosa.http.retry_times'),
                retrySleepMs: (int) config('prosa.http.retry_sleep_ms'),
                merchantParams: MerchantData::fromConfig()->toParams(),
            );
        });

        $this->app->singleton(PaymentService::class, fn ($app) => new PaymentService(
            http: $app->make(ProsaHttpClient::class),
        ));

        $this->app->singleton(TokenizationService::class, fn ($app) => new TokenizationService(
            http: $app->make(ProsaHttpClient::class),
        ));

        $this->app->singleton(RecurringService::class, fn ($app) => new RecurringService(
            http: $app->make(ProsaHttpClient::class),
        ));

        $this->app->singleton(BackofficeService::class, fn ($app) => new BackofficeService(
            http: $app->make(ProsaHttpClient::class),
        ));

        $this->app->singleton(OxxoService::class, fn ($app) => new OxxoService(
            http: $app->make(ProsaHttpClient::class),
        ));

        // ── Checkout completers (3DS / post-pago) ──
        $this->app->bind('prosa.completer.solicitud_franquicia', fn () => new SolicitudCheckout(
            flowName: 'solicitud_franquicia',
            tipoSolicitud: 'franquicia',
            successRouteOrPath: '/franquicia/solicitud/confirmacion',
            failRouteOrPath: '/franquicia/solicitud',
        ));

        $this->app->bind('prosa.completer.solicitud_distribuidor', fn () => new SolicitudCheckout(
            flowName: 'solicitud_distribuidor',
            tipoSolicitud: 'distribuidor',
            successRouteOrPath: '/distribucion/solicitud/confirmacion',
            failRouteOrPath: '/distribucion/solicitud',
        ));

        $this->app->bind('prosa.completer.solicitud_distribuidor_link', fn () => new SolicitudCheckout(
            flowName: 'solicitud_distribuidor_link',
            tipoSolicitud: 'distribuidor',
            successRouteOrPath: '/distribucion/solicitud/confirmacion',
            failRouteOrPath: '/distribucion/solicitud',
        ));

        $this->app->bind('prosa.completer.solicitud_pats', fn () => new SolicitudCheckout(
            flowName: 'solicitud_pats',
            tipoSolicitud: 'pats',
            successRouteOrPath: '/pasaporte',
            failRouteOrPath: '/pats/registro',
        ));

        $this->app->tag([
            PagosCheckout::class,
            PagosRenovacionCheckout::class,
            PagosRecurringCheckout::class,
            AdquirirCheckout::class,
            'prosa.completer.solicitud_franquicia',
            'prosa.completer.solicitud_distribuidor',
            'prosa.completer.solicitud_distribuidor_link',
            'prosa.completer.solicitud_pats',
        ], 'prosa.checkout.completers');

        $this->app->singleton(CheckoutManager::class, fn ($app) => new CheckoutManager(
            $app->tagged('prosa.checkout.completers'),
        ));
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/prosa.php',
            'prosa'
        );
    }
}
