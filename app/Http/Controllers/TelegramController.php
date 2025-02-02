<?php

namespace App\Http\Controllers;

use App\Models\MovieCode;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use App\Models\BotUser;

class TelegramController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(config('services.telegram.bot_token'));
    }

    public function handleWebhook(Request $request)
    {
        $update = $request->all(); // Telegramdan kelgan ma'lumotlarni olish

        $chatId = $update['message']['chat']['id'];
        $text = $update['message']['text'];
        \Log::info("chatId: " . $chatId);
        // Foydalanuvchi ro'yxatdan o'tadi
        $user = BotUser::firstOrCreate(
            ['telegram_id' => $chatId],
            [
                'telegram_id' => $update['message']['chat']['id'] ?? 'unknown',
                'first_name' => $update['message']['chat']['first_name'] ?? 'unknown',
                'last_name' => $update['message']['chat']['last_name'] ?? 'unknown',
                'username' => $update['message']['chat']['username'] ?? 'unknown',
            ]
        );

        // Kino kodi bilan so'rov
        if (is_numeric($text)) {  // Raqam yuborilganini tekshiramiz
            $movieCode = $text;

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
                    'text' => "Kechirasiz, bunday kodli kino mavjud emas.",
                ]);
            }
        } else {
            // Foydalanuvchiga kino kodi yuborilishini so'rash
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Iltimos, kino kodi yuboring.",
            ]);
        }

        return response()->json(['status' => 'ok']); // Telegramga javob yuborish
    }
}
