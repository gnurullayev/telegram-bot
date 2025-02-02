<?php

use App\Http\Controllers\TelegramController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/telegram/webhook', function () {
    $updates = Telegram::commandsHandler(true);

    return 'OK';
});

// Route::post('/telegram/webhook', [TelegramController::class, 'handleWebhook'])->middleware(VerifyCsrfToken::class);