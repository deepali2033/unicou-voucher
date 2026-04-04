<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::match(['get', 'post'], '/webhook/wise', function (Request $request) {

    \Log::info('Wise Webhook Data:', $request->all());

    return response('OK', 200);
});
