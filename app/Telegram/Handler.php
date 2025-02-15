<?php

namespace App\Telegram;

use App\Models\BotUser;
use App\Models\MovieCode;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Stringable;

class Handler extends WebhookHandler
{
    private $token;
    private $channel_username;
    private $channel_link;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->channel_username = "romantic_movies1";
        $this->channel_link = "https://t.me/{$this->channel_username}";
    }

    public function hello(): void
    {
        $this->reply("salom botga hush kelibsiz");
    }

    public function start(): void
    {

        $user = $this->message->from();
        if ($user) {
            $user_id = $user->id();

            // if (!$this->isUserMember($user_id)) {
            //     Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
            //         'chat_id' => $user_id, // yoki kanal chat_id
            //         'text' => "📢 Iltimos botimizdan foydalanish uchun, bizning kanalimizga azo bo‘ling \nПожалуйста, подпишитесь на наш канал, чтобы использовать нашего бота\nPlease subscribe to our channel to use our bot.\nBot manzili ➡️ <a href='{$this->channel_link}'>Movies</a>\Адрес бота ➡️ <a href='{$this->channel_link}'>Movies</a>\Bot address ➡️ <a href='{$this->channel_link}'>Movies</a>",
            //         'parse_mode' => 'HTML'
            //     ]);
            //     return;
            // }


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
            $this->reply("Iltimos, faqat kino kodini yuboring (masalan: 12345). \n Please send only the movie code (for example: 12345).");
        } else {
            \Log::info("❌ Foydalanuvchi ma'lumotlarini olishda xatolik yuz berdi.");
        }
    }

    public function bot_users(): void
    {
        $query = BotUser::query()->orderBy('created_at', 'desc');
        $total = $query->count(); // Har sahifada 10 ta foydalanuvchi
        $users = $query->paginate(20); // Har sahifada 10 ta foydalanuvchi

        if ($users->isEmpty()) {
            $this->reply("📌 Hozircha ro'yxatda foydalanuvchilar yo'q.");
            return;
        }

        $message = "📌 *Bot foydalanuvchilari:*\n\n";
        foreach ($users as $user) {
            $message .= "🆔 ID: {$user->telegram_id}\n";
            $message .= "👤 Ism: {$user->first_name}\n";
            $message .= "---------------------\n";
        }


        $this->reply($message);
        $this->reply("Total: " . $total);
    }


    public function set_menu(): void
    {
        $this->setCommands();
    }

    public function handleChatMessage(Stringable $text): void
    {
        $user = $this->message->from();
        if (!$user) {
            \Log::info("❌ Xatolik: foydalanuvchi ma'lumotlari olinmadi.");
            return;
        }

        $user_id = $user->id();

        // Kanalga azo ekanligini tekshirish
        // if (!$this->isUserMember($user_id)) {
        //     Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
        //         'chat_id' => $user_id, // yoki kanal chat_id
        //         'text' => "📢 Iltimos botimizdan foydalanish uchun, bizning kanalimizga azo bo‘ling \nПожалуйста, подпишитесь на наш канал, чтобы использовать нашего бота\nPlease subscribe to our channel to use our bot.\nBot manzili ➡️ <a href='{$this->channel_link}'>Movies</a>\Адрес бота ➡️ <a href='{$this->channel_link}'>Movies</a>\Bot address ➡️ <a href='{$this->channel_link}'>Movies</a>",
        //         'parse_mode' => 'HTML'
        //     ]);
        //     return;
        // }


        $movieCode = (string) $text;

        if (ctype_digit($movieCode)) {
            $movie = MovieCode::where('id', $movieCode)->first();

            if ($movie) {
                $this->reply("🎬 Link: {$movie->link}");
            } else {
                $this->reply("⚠️ Afsuski, siz so'ragan kino topilmadi. \n Unfortunately, the movie you requested was not found.");
            }
        } else {
            $this->reply("⚠️ Iltimos, faqat raqam kiriting (masalan: 12345).\n ⚠️ Please enter only numbers (for example: 12345).");
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

    // private function isUserMember($user_id): bool
    // {
    //     $channel_id = '@' . $this->channel_username; // Kanalning username'ini kiriting

    //     $url = "https://api.telegram.org/bot{$this->token}/getChatMember?chat_id={$channel_id}&user_id={$user_id}";

    //     $response = file_get_contents($url);
    //     $data = json_decode($response, true);

    //     if (isset($data['result']['status'])) {
    //         $status = $data['result']['status'];
    //         return in_array($status, ['member', 'administrator', 'creator']);
    //     }

    //     return false;
    // }
}
