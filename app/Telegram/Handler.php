<?php

namespace App\Telegram;

use App\Models\BotUser;
use App\Models\MovieCode;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\Http;
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

    public function bot_users(): void
    {
        $users = BotUser::query()->paginate(10);

        if ($users->isEmpty()) {
            $this->reply("📌 Hozircha ro'yxatda foydalanuvchilar yo'q.");
            return;
        }

        $messageParts = [];
        $currentMessage = "📌 *Bot foydalanuvchilari:*\n\n";

        foreach ($users as $user) {
            $userInfo = "🆔 ID: {$user->telegram_id}\n";
            $userInfo .= "👤 Ism: {$user->first_name} {$user->last_name}\n";
            $userInfo .= "📛 Username: @" . ($user->username ?? "Noma'lum") . "\n";
            $userInfo .= "---------------------\n";

            if (mb_strlen($currentMessage . $userInfo) > 4000) { // 4000 belgidan oshmasligi uchun
                $messageParts[] = $currentMessage;
                $currentMessage = "";
            }

            $currentMessage .= $userInfo;
        }

        $messageParts[] = $currentMessage; // Oxirgi bo‘lakni qo‘shish

        foreach ($messageParts as $part) {
            $this->reply($part);
        }
    }

    public function set_menu(): void
    {
        $this->setCommands();
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

    public function setCommands(): void
    {
        $token = config('services.telegram.bot_token'); // Tokenni .env fayldan olish
        $url = "https://api.telegram.org/bot{$token}/setMyCommands";

        $commands = [
            ['command' => 'start', 'description' => "Botni ishga tushirish"],
            // ['command' => 'movies', 'description' => "Mashhur kinolar"],
            // ['command' => 'search', 'description' => "Kino qidirish"],
            // ['command' => 'help', 'description' => "Yordam"],
        ];

        $response = Http::post($url, ['commands' => json_encode($commands)]);

        if ($response->successful()) {
            $this->reply('✅ Telegram buyruqlari muvaffaqiyatli o‘rnatildi!');
        } else {
            $this->reply('❌ Xatolik yuz berdi: ' . $response->body());
        }
    }
}
