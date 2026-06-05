<?php

use App\Http\Controllers\Prosa\ProsaPaymentController;
use App\Http\Controllers\Prosa\ProsaTokenController;
use App\Http\Controllers\Prosa\ProsaWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('prosa')->group(function () {

    // ── Ventas ────────────────────────────────────────────────
    Route::post('/sale/one-step', [ProsaPaymentController::class, 'oneStepSale'])
        ->name('prosa.sale.one-step');

    Route::post('/sale/recurring', [ProsaPaymentController::class, 'recurringSale'])
        ->name('prosa.sale.recurring');

    Route::post('/sale/cash', [ProsaPaymentController::class, 'cashSale'])
        ->name('prosa.sale.cash');

    // ── Post-venta ────────────────────────────────────────────
    Route::post('/refund', [ProsaPaymentController::class, 'refund'])
        ->name('prosa.refund');

    Route::post('/reversal', [ProsaPaymentController::class, 'reversal'])
        ->name('prosa.reversal');

    // ── Tokenización ──────────────────────────────────────────
    Route::prefix('token')->group(function () {
        Route::get('/cards', [ProsaTokenController::class, 'cards'])
            ->name('prosa.token.cards');

        Route::post('/generate', [ProsaTokenController::class, 'generate'])
            ->name('prosa.token.generate');

        Route::post('/sale', [ProsaTokenController::class, 'sale'])
            ->name('prosa.token.sale');

        Route::post('/reversal', [ProsaTokenController::class, 'reversal'])
            ->name('prosa.token.reversal');

        Route::post('/refund', [ProsaTokenController::class, 'refund'])
            ->name('prosa.token.refund');

        Route::patch('/{id}/default', [ProsaTokenController::class, 'setDefault'])
            ->name('prosa.token.default');

        Route::delete('/{id}', [ProsaTokenController::class, 'destroy'])
            ->name('prosa.token.destroy');
    });

    // ── Webhook ───────────────────────────────────────────────
    Route::post('/webhook', [ProsaWebhookController::class, 'receive'])
        ->name('prosa.webhook')
        ->withoutMiddleware(['auth', \App\Http\Middleware\VerifyCsrfToken::class]);

});
