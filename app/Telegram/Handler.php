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

        $this->reply(json_encode($this->bot->info()['id']));
        // $this->reply(json_encode($this->message->id()));
        $chatId = $this->message->id();
        $telegraph = new Telegraph();
        // $updates = $telegraph->getUpdates();
        // $chatId = $updates[0]->getMessage()->getChat()->getId;
        // Video yuborish
        try {
            $telegraph->chat($chatId)
                ->video("https://topmovie.sgp1.cdn.digitaloceanspaces.com/Qizil-g'unchalar/G'unchalar%2010-qism%20480p%20O'zbek%20tilida.mp4")
                ->send();
        } catch (\Exception $e) {
            $this->reply('Xato yuz berdi: ' . $e->getMessage());
        }
    }
}
