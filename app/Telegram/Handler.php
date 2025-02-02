<?php

namespace App\Telegram;

use App\Models\BotUser;
use App\Models\MovieCode;
use DefStudio\Telegraph\Facades\Telegraph as FacadesTelegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;

class Handler extends WebhookHandler
{
    public function hello(): void
    {
        $this->reply("salom botga hush kelibsiz");
    }

    public function start(): void
    {
        $chat_id = $this->message->id(); // Foydalanuvchining chat ID sini olish
        $text = $this->message->text(); // Foydalanuvchidan kelgan matnni olish

        // Foydalanuvchini ro'yxatdan o'tkazish
        $user = BotUser::firstOrCreate(
            ['telegram_id' => $chat_id],
            [
                'telegram_id' => $chat_id,
                'first_name' => $this->message->chat()->first_name ?? 'unknown',
                'last_name' => $this->message->chat()->last_name ?? 'unknown',
                'username' => $this->message->chat()->username ?? 'unknown',
            ]
        );

        // Kino kodi yuborilganini tekshirish
        if (preg_match('/^(\d+)$/', $text, $matches)) {
            $movieCode = $matches[1]; // Kino kodi sifatida raqamni olish

            \Log::info("Movie Code: " . $movieCode);

            // Kino kodi bo'yicha ma'lumotni qidirish
            $movie = MovieCode::where('id', $movieCode)->first();

            if ($movie) {
                // Kino topilgan bo'lsa, uning linkini yuborish
                FacadesTelegraph::chat($chat_id)
                    ->text("Link: {$movie->link}")
                    ->send();
            } else {
                // Kino topilmasa
                FacadesTelegraph::chat($chat_id)
                    ->text("Afsuski, siz so'ragan kino topilmadi.")
                    ->send();
            }
        } else {
            // Kino kodi noto'g'ri formatda bo'lsa
            FacadesTelegraph::chat($chat_id)
                ->text("Iltimos, faqat kino kodini yuboring (masalan: 12345).")
                ->send();
        }
    }
}
