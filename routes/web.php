<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;


Route::get('/', function () {
    return view('welcome');
});


Route::post('/telegram/webhook', function () {
    $updates = Telegram::commandsHandler(true);

    return 'OK';
});
