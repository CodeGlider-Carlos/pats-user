<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Feenicia\FeeniciaTransactionController;
use App\Http\Controllers\Feenicia\FeeniciaWebhookController;
use App\Http\Controllers\Feenicia\FeeniciaTokenController;

Route::prefix('feenicia')->group(function () {

    // ── Ventas ────────────────────────────────────────────────
    Route::post('/sale/one-step', [FeeniciaTransactionController::class, 'oneStepSale'])
         ->name('feenicia.sale.one-step');

    Route::post('/sale/cash', [FeeniciaTransactionController::class, 'cashSale'])
         ->name('feenicia.sale.cash');

    Route::post('/sale/recurring', [FeeniciaTransactionController::class, 'recurringSale'])
         ->name('feenicia.sale.recurring');

    // ── Post-venta ────────────────────────────────────────────
    Route::post('/refund',       [FeeniciaTransactionController::class, 'refund'])
         ->name('feenicia.refund');

    Route::post('/cancellation', [FeeniciaTransactionController::class, 'cancellation'])
         ->name('feenicia.cancellation');

    Route::post('/reversal',     [FeeniciaTransactionController::class, 'reversal'])
         ->name('feenicia.reversal');

    // ── Tokenización ──────────────────────────────────────────
    Route::prefix('token')->group(function () {

        Route::get('/cards',          [FeeniciaTokenController::class, 'cards'])
             ->name('feenicia.token.cards');

        Route::post('/generate',      [FeeniciaTokenController::class, 'generate'])
             ->name('feenicia.token.generate');

        Route::post('/sale',          [FeeniciaTokenController::class, 'sale'])
             ->name('feenicia.token.sale');

        Route::post('/reversal',      [FeeniciaTokenController::class, 'reversal'])
             ->name('feenicia.token.reversal');

        Route::post('/refund',        [FeeniciaTokenController::class, 'refund'])
             ->name('feenicia.token.refund');

        Route::patch('/{id}/default', [FeeniciaTokenController::class, 'setDefault'])
             ->name('feenicia.token.default');

        Route::delete('/{id}',        [FeeniciaTokenController::class, 'destroy'])
             ->name('feenicia.token.destroy');

    });

    // ── Webhook ───────────────────────────────────────────────
    Route::post('/webhook', [FeeniciaWebhookController::class, 'receive'])
         ->name('feenicia.webhook')
         ->middleware('feenicia.webhook')
         ->withoutMiddleware(['auth', \App\Http\Middleware\VerifyCsrfToken::class]);

});