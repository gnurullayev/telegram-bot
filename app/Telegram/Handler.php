<?php

namespace App\Telegram;

use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;

class Handler extends WebhookHandler
{
    public function hello(): void
    {
        $this->reply("salom botga hush kelibsiz");
    }

    public function start(): void
    {
        // $this->re("salom botga hush kelibsiz");
        Telegraph::video("https://topmovie.sgp1.cdn.digitaloceanspaces.com/Qizil-g'unchalar/G'unchalar%2010-qism%20480p%20O'zbek%20tilida.mp4")->send();
    }
}
