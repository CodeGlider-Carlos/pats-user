<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Feenicia\FeeniciaCryptoService;
use App\Services\Feenicia\FeeniciaSignatureService;
use App\Services\Feenicia\FeeniciaHttpClient;
use App\Services\Feenicia\OneStepSaleService;
use App\Services\Feenicia\CashSaleService;
use App\Services\Feenicia\RecurringBillingService;
use App\Services\Feenicia\RefundService;
use App\Services\Feenicia\CancellationService;
use App\Services\Feenicia\ReversalService;

class FeeniciaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ── Servicios base ──────────────────────────────────────
        $this->app->singleton(FeeniciaCryptoService::class, function () {
            return new FeeniciaCryptoService(
                key: config('feenicia.keys.request.key'),
                iv:  config('feenicia.keys.request.iv'),
            );
        });

        $this->app->singleton(FeeniciaSignatureService::class, function () {
            return new FeeniciaSignatureService(
                signatureKey: config('feenicia.keys.signature.key'),
                signatureIv:  config('feenicia.keys.signature.iv'),
                merchant:     config('feenicia.merchant'),
            );
        });

        $this->app->singleton(FeeniciaHttpClient::class, function ($app) {
            return new FeeniciaHttpClient(
                baseUrl:          config('feenicia.base_url'),
                signatureService: $app->make(FeeniciaSignatureService::class),
                timeout:          config('feenicia.http.timeout'),
                connectTimeout:   config('feenicia.http.connect_timeout'),
            );
        });

        // ── Servicios de transacción ────────────────────────────
        $this->app->singleton(OneStepSaleService::class, function ($app) {
            return new OneStepSaleService(
                http:   $app->make(FeeniciaHttpClient::class),
                crypto: $app->make(FeeniciaCryptoService::class),
            );
        });

        // RecurringBillingService se registra antes de CashSaleService
        // porque CashSaleService lo inyecta para reutilizar los pasos compartidos
        $this->app->singleton(RecurringBillingService::class, function ($app) {
            return new RecurringBillingService(
                http:   $app->make(FeeniciaHttpClient::class),
                crypto: $app->make(FeeniciaCryptoService::class),
            );
        });

        $this->app->singleton(CashSaleService::class, function ($app) {
            return new CashSaleService(
                http:         $app->make(FeeniciaHttpClient::class),
                crypto:       $app->make(FeeniciaCryptoService::class),
                sharedSteps:  $app->make(RecurringBillingService::class),
            );
        });

        $this->app->singleton(RefundService::class, function ($app) {
            return new RefundService(
                http:   $app->make(FeeniciaHttpClient::class),
                crypto: $app->make(FeeniciaCryptoService::class),
            );
        });

        $this->app->singleton(CancellationService::class, function ($app) {
            return new CancellationService(
                http:   $app->make(FeeniciaHttpClient::class),
                crypto: $app->make(FeeniciaCryptoService::class),
            );
        });

        $this->app->singleton(ReversalService::class, function ($app) {
            return new ReversalService(
                http:   $app->make(FeeniciaHttpClient::class),
                crypto: $app->make(FeeniciaCryptoService::class),
            );
        });
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/feenicia.php',
            'feenicia'
        );
    }
}
