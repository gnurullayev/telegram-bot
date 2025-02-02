<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use App\Models\BotUser;
use App\Models\Movie;
use App\Models\MovieCode;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:polling2';
    protected $description = 'Telegram bot uchun polling mexanizmi';

    protected $telegram;

    public function __construct()
    {
        parent::__construct();
        $this->telegram = new Api(config('services.telegram.bot_token'));
    }

    public function handle()
    {
        $offset = 0;

        while (true) {
            try {
                $updates = $this->telegram->getUpdates([
                    'offset' => $offset,
                    'timeout' => 10, // 10 soniya kutish
                ]);

                foreach ($updates as $update) {
                    $offset = $update['update_id'] + 1; // Keyingi xabar uchun offsetni oshirish

                    if (!isset($update['message'])) {
                        continue;
                    }

                    $chatId = $update['message']['chat']['id'];
                    $text = $update['message']['text'];

                    $user = BotUser::firstOrCreate(
                        ['telegram_id' => $chatId],
                        [
                            'telegram_id' => $update['message']['chat']['id'] ?? 'unknown',
                            'first_name' => $update['message']['chat']['first_name'] ?? 'unknown',
                            'last_name' => $update['message']['chat']['last_name'] ?? 'unknown',
                            'username' => $update['message']['chat']['username'] ?? 'unknown',
                        ]
                    );

                    if (preg_match('/^(\d+)$/', $text, $matches)) {
                        $movieCode = $matches[1]; // Kino kodi sifatida raqamni olish


                        // Kino kodi bo'yicha ma'lumotni qidirish
                        $movie = MovieCode::where('id', $movieCode)->first();

                        if ($movie) {
                            // Kino topilgan bo'lsa, uning linkini yuborish
                            $this->telegram->sendMessage([
                                'chat_id' => $chatId,
                                'text' => "Link: {$movie->link}",
                            ]);
                        } else {
                            // Kino topilmasa
                            $this->telegram->sendMessage([
                                'chat_id' => $chatId,
                                'text' => "Afsuski, siz so'ragan kino topilmadi.",
                            ]);
                        }
                    } else {
                        // Kino kodi to'g'ri formatda bo'lmasa
                        $this->telegram->sendMessage([
                            'chat_id' => $chatId,
                            'text' => "Iltimos, faqat kino kodini yuboring (masalan: 12345).",
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $this->error("Xatolik: " . $e->getMessage());
                sleep(5); // Xatolik bo'lsa, 5 soniya kutish
            }
        }
    }
}
