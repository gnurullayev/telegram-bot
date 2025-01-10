<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('menu', function () {
    /** @var \DefStudio\Telegraph\Models\TelegraphBot $telegraphBot */
    $bot = \DefStudio\Telegraph\Models\TelegraphBot::find(1);

    $bot->registerCommands([
        'hello' => 'salom botga hush kelibisiz',
        'start' => 'sizga nima yordam bera olaman'
    ])->send();
});
