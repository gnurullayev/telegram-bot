<?php

namespace App\Telegram;

use DefStudio\Telegraph\Facades\Telegraph as FacadesTelegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Telegraph;

class Handler extends WebhookHandler
{
    public function hello(): void
    {
        $this->reply("salom botga hush kelibsiz");
    }

    public function start(): void
    {
        // $this->bo("salom botga hush kelibsiz");

        $this->reply(json_encode($this->bot->info()));
        $this->reply(json_encode($this->message->id()));
        // $chat_id = $this->message->id();
        // $telegraph = new Telegraph();
        // $updates = $telegraph->getUpdates();
        // $chat_id = $updates[0]->getMessage()->getChat()->getId;
        // Video yuborish
        try {
            $this->reply("Ko'proq ma'lumot uchun https://topmovie.sgp1.cdn.digitaloceanspaces.com/Qizil-g'unchalar/G'unchalar%2010-qism%20480p%20O'zbek%20tilida.mp4 saytiga tashrif buyuring!");

            $this->reply("salom botga hush kelibsiz 2");
        } catch (\Exception $e) {
            $this->reply('Xato yuz berdi: ' . $e->getMessage());
        }
    }

    // public function start(): void
    // {
    //     $chat_id = $this->message->id(); // Foydalanuvchining chat ID sini olish
    //     $chat = $this->message?->chat(); // Foydalanuvchining chat ID sini olish
    //     // $first_name = $this->message->chat() ? $this->message->chat()['first_name'] : "unknown"; // Foydalanuvchining ismini olish
    //     // $last_name = $this->message->chat() ? $this->message->chat()['last_name'] : "unknown"; // Foydalanuvchining familiyasini olish
    //     // $username = $this->message->chat() ? $this->message->chat()['username'] : "unknown";
    //     try {

    //         // Yana bir xabar bilan tasdiqlashsadf
    //         $this->reply(" " . $chat);
    //     } catch (\Exception $e) {
    //         // Xatolikni qaytarish
    //         $this->reply('Xato yuz berdi: ' . $e->getMessage());
    //     }
    // }
}





// public function start(): void
// {
//     $chat_id = $this->message->id(); // Foydalanuvchining chat ID sini olish
//     $text = $this->message->text(); // Foydalanuvchidan kelgan matnni olish

//     // Foydalanuvchini ro'yxatdan o'tkazish
//     $user = BotUser::firstOrCreate(
//         ['telegram_id' => $chat_id],
//         [
//             'telegram_id' => $chat_id,
//             'first_name' => $this->message->chat()->first_name ?? 'unknown',
//             'last_name' => $this->message->chat()->last_name ?? 'unknown',
//             'username' => $this->message->chat()->username ?? 'unknown',
//         ]
//     );

//     // Kino kodi yuborilganini tekshirish
//     if (preg_match('/^(\d+)$/', $text, $matches)) {
//         $movieCode = $matches[1]; // Kino kodi sifatida raqamni olish

//         \Log::info("Movie Code: " . $movieCode);

//         // Kino kodi bo'yicha ma'lumotni qidirish
//         $movie = MovieCode::where('id', $movieCode)->first();

//         if ($movie) {
//             // Kino topilgan bo'lsa, uning linkini yuborish
//             FacadesTelegraph::chat($chat_id)
//                 ->text("Link: {$movie->link}")
//                 ->send();
//         } else {
//             // Kino topilmasa
//             FacadesTelegraph::chat($chat_id)
//                 ->text("Afsuski, siz so'ragan kino topilmadi.")
//                 ->send();
//         }
//     } else {
//         // Kino kodi noto'g'ri formatda bo'lsa
//         FacadesTelegraph::chat($chat_id)
//             ->text("Iltimos, faqat kino kodini yuboring (masalan: 12345).")
//             ->send();
//     }
// }
