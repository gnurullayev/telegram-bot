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
        // $this->reply("salom botga hush kelibsiz");
        $telegraph = new Telegraph();
        // Video yuborish
        try {
            $telegraph->video("https://topmovie.sgp1.cdn.digitaloceanspaces.com/Qizil-g'unchalar/G'unchalar%2010-qism%20480p%20O'zbek%20tilida.mp4")
                ->send();
        } catch (\Exception $e) {
            $this->reply('Xato yuz berdi: ' . $e->getMessage());
        }
    }
}
