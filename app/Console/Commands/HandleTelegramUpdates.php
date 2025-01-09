<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class HandleTelegramUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:updates';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle Telegram updates via long-polling';

    public function handle()
    {
        $updates = Telegram::getUpdates();

        foreach ($updates as $update) {
            // Xabarlarni qayta ishlash
            $message = $update['message']['text'] ?? 'No text';
            $chatId = $update['message']['chat']['id'];

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Siz yuborgan xabar: $message",
            ]);
        }
    }
}
