<?php

use App\Http\Controllers\Api\DistribucionLinkApiController;
use App\Http\Controllers\Api\FranquiciaLinkApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ─── Distribucion Links API (protegida con X-Api-Key) ─────────────────────────
Route::middleware('auth.api-key')->group(function () {
    Route::get('/distribucion-links',        [DistribucionLinkApiController::class, 'index']);
    Route::post('/distribucion-links',       [DistribucionLinkApiController::class, 'store']);
    Route::delete('/distribucion-links/{id}',[DistribucionLinkApiController::class, 'destroy'])->where('id', '[0-9]+');

    Route::get('/franquicia-links',        [FranquiciaLinkApiController::class, 'index']);
    Route::post('/franquicia-links',       [FranquiciaLinkApiController::class, 'store']);
    Route::delete('/franquicia-links/{id}',[FranquiciaLinkApiController::class, 'destroy'])->where('id', '[0-9]+');
});
