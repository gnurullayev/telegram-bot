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

        // $this->reply(json_encode($this->bot->info()['id']));
        // $this->reply(json_encode($this->message->id()));
        $chat_id = $this->message->id();
        $telegraph = new Telegraph();
        // $updates = $telegraph->getUpdates();
        // $chat_id = $updates[0]->getMessage()->getChat()->getId;
        // Video yuborish
        try {
            // $this->reply("salom botga hush kelibsiz 1");
            // $telegraph->chat($chat_id)->video("https://topmovie.sgp1.cdn.digitaloceanspaces.com/Qizil-g'unchalar/G'unchalar%2010-qism%20480p%20O'zbek%20tilida.mp4")->send();
            FacadesTelegraph::chat($chat_id) // Telegram chat ID-ni kiriting
                ->message("Ko'proq ma'lumot uchun <a href='https://topmovie.me'>Topmovie.me</a> saytiga tashrif buyuring!") // HTML formatdagi matn
                ->send();
        } catch (\Exception $e) {
            $this->reply('Xato yuz berdi: ' . $e->getMessage());
        }
    }
}
