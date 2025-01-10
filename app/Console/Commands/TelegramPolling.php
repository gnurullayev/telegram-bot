<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:polling';
    protected $description = 'Telegram bot uchun polling mexanizmi';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $telegram = new Api("7701483844:AAEgpS5HDhfS3A7ncHLHtGwQl-yP_BppIl0"); // Bot tokeningizni kiriting

        while (true) {
            $updates = $telegram->getUpdates(); // Yangi xabarlarni olish

            foreach ($updates as $update) {
                $chatId = $update['message']['chat']['id'];
                $message = $update['message']['text'];

                // Javob qaytarish
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Sizning xabaringiz: $message",
                ]);
            }

            sleep(1); // Bot so‘rovlar o‘rtasida kutadi
        }
    }
}
