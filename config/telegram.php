<?php

return [
    'default' => 'common',

    'bots' => [
        'common' => [
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'async_requests' => false,
            'http' => [
                'verify' => false, // Sertifikatni tekshirishni o'chirish
            ],
        ],
    ],

    'log_channel' => env('TELEGRAM_LOG_CHANNEL', 'null'),
];
