<?php

namespace App\Telegram;

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

        // $this->reply(json_encode($this->bot->info()['id']));
        // $this->reply(json_encode($this->message->id()));
        $chat_id = $this->message->id();
        $telegraph = new Telegraph();
        // $updates = $telegraph->getUpdates();
        // $chat_id = $updates[0]->getMessage()->getChat()->getId;
        // Video yuborish
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find(1);
        try {
            $telegraph->chat($chat_id)
                ->video(storage_path('app/public/videos/ajal_poygasi.mp4'))
                // ->caption("🎬 <b>Filming nomi:</b> Ajal poygasi\n\n/rand - 🎲 Random kinolar\n/top - 🏆 Top kinolar\n/last - ✨ Oxirgi yuklanganlar\n/help - 📞 Qo‘llab-quvvatlash\n/dev - 👨‍💻 Dasturchi")
                ->keyboard([
                    ['🎬 Ko‘proq Filmlar', '📥 Saqlab qo‘yish'], // Inline tugmalar
                ])
                ->send();
            // $bot->chat($chatId)
            //     ->video("https://topmovie.sgp1.cdn.digitaloceanspaces.com/Qizil-g'unchalar/G'unchalar%2010-qism%20480p%20O'zbek%20tilida.mp4")
            //     ->send();
        } catch (\Exception $e) {
            $this->reply('Xato yuz berdi: ' . $e->getMessage());
        }
    }
}
