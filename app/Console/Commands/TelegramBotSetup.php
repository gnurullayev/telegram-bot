<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotSetup extends Command
{
    protected $signature = 'telegram:setup';
    protected $description = 'Telegram botni sozlash';

    public function handle()
    {
        // Webhook URL o'rnatish
        $webhookUrl = 'https://pornhubm.me/telegram/webhook'; // URLni o'zgartiring

        // Webhookni sozlash
        $response = Telegram::setWebhook([
            'url' => $webhookUrl,
        ]);

        $this->info("Webhook o'rnatildi: " . $response);
    }
}
