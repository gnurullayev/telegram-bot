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

    public function bot_users(int $page = 1): void
    {
        $perPage = 5;
        $users = BotUser::query()->paginate($perPage, ['*'], 'page', $page);

        if ($users->isEmpty()) {
            $this->reply("📌 Hozircha ro'yxatda foydalanuvchilar yo'q.");
            return;
        }

        $message = "📌 *Bot foydalanuvchilari (Sahifa: $page):*\n\n";

        $this->reply($message);
        // foreach ($users as $user) {
        //     $message .= "🆔 ID: {$user->telegram_id}\n";
        //     $message .= "👤 Ism: {$user->first_name}\n";
        //     $message .= "📛 Username: @" . ($user['username'] ?? "Noma'lum") . "\n";
        //     $message .= "---------------------\n";
        // }

        // Inline tugmalarni yaratish
        // $keyboard = Keyboard::make();
        // if ($users->previousPageUrl()) {
        //     $keyboard->row([
        //         Button::make('⬅ Oldingi')->action('bot_users')->param('page', $page - 1),
        //     ]);
        // }
        // if ($users->nextPageUrl()) {
        //     $keyboard->row([
        //         Button::make('Keyingi ➡')->action('bot_users')->param('page', $page + 1),
        //     ]);
        // }

        // Xabarni tugmalar bilan jo‘natish
        // $this->reply($message);
    }

    public function handleCallbackQuery(): void
    {
        $callbackData = $this->callbackQuery->data();

        if (str_starts_with($callbackData, "bot_users:")) {
            $page = (int) str_replace("bot_users:", "", $callbackData);
            $this->bot_users($page);
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
