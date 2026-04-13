<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\BankController;
use App\Http\Controllers\Api\KuickpayController;

Route::match(['get', 'post'], '/webhook/wise', function (Request $request) {
    \Log::info('Wise Webhook Data:', $request->all());
    return response('OK', 200);
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('/BillInquiry', [KuickpayController::class, 'billInquiry']);
    Route::post('/BillPayment', [KuickpayController::class, 'billPayment']);
});
Route::get('/create-bank-session', [BankController::class, 'createSession']);
