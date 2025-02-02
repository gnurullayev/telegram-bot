<?php

namespace App\Telegram;

use App\Models\BotUser;
use App\Models\MovieCode;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Stringable;

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
        $text = $this->message->text ?? ''; // Agar bo'sh bo'lsa, default qiymat ''

        if (empty($text)) {
            $this->reply("⚠️ Iltimos, kino kodini yuboring.");
            return;
        }

        if (preg_match('/^\d+$/', $text)) {
            $this->reply("🎬 Kino topildi! Link: {$text}");
        } else {
            $this->reply("⚠️ Iltimos, faqat kino kodini yuboring (masalan: 12345).");
        }
    }


    public function test(string $text): void
    {
        $this->reply("⚠️ test" . " " . $text);
    }

    public function handleChatMessage(Stringable $text): void
    {
        $movieCode = (string) $text;

        if (ctype_digit($movieCode)) {
            $movie = MovieCode::where('id', $movieCode)->first();

            if ($movie) {
                $this->reply("🎬 Kino topildi! Link: {$movie->link}");
            } else {
                $this->reply("⚠️ Afsuski, siz so'ragan kino topilmadi.");
            }
        } else {
            $this->reply("⚠️ Iltimos, faqat raqam kiriting (masalan: 12345).");
        }
    }
}
