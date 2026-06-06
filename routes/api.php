<?php

use App\Http\Controllers\PaymentController;
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

Route::post('webhooks/pagbank', [PaymentController::class, 'webhook'])->name('webhook.pagbank');

Route::middleware('AuthTenant')->prefix('pay')->group(function () {
    Route::post('generate-pix',  [PaymentController::class, 'generatePix']);
    Route::get('pix-status/{payment}',  [PaymentController::class, 'pixStatus']);
    Route::post('card',  [PaymentController::class, 'payWithCard']);
    Route::post('boleto',  [PaymentController::class, 'payWithBoleto']);
    Route::get('boleto/{payment}', [PaymentController::class, 'showPendingBoleto']);
});
