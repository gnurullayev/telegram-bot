<?php

namespace App\Telegram;

use App\Models\BotUser;
use DefStudio\Telegraph\Handlers\WebhookHandler;

class Handler extends WebhookHandler
{
    public function hello(): void
    {
        $this->reply("salom botga hush kelibsiz");
    }

    public function start(): void
    {

        $user = $this->message->from();
        if ($user) {
            $user_id = $user->id();
            $first_name = $user->firstName();
            $last_name = $user->lastName() ?? 'Noma’lum';
            $username = $user->username() ?? 'Noma’lum';

            $user = BotUser::firstOrCreate(
                ['telegram_id' => $user_id],
                [
                    'telegram_id' => $user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'username' => $username,
                ]
            );
            $this->reply("❌Iltimos, faqat kino kodini yuboring (masalan: 12345).");
        } else {
            $this->reply("❌ Foydalanuvchi ma'lumotlarini olishda xatolik yuz berdi.");
        }
    }

    public function checkMovie(): void
    {
        $text = $this->message->text(); // Foydalanuvchi kiritgan kino kodi

        if (preg_match('/^\d+$/', $text)) {

            if ($text) {
                $this->reply("🎬 Kino topildi! Link: {$text}");
            } else {
                $this->reply("❌ Afsuski, siz soʻragan kino topilmadi.");
            }
        } else {
            $this->reply("⚠️ Iltimos, faqat kino kodini yuboring (masalan: 12345).");
        }
    }
}
