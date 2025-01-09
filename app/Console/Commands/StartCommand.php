<?php

namespace App\Console\Commands;

use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = "start";
    protected string $description = "Start command to welcome users";

    public function handle()
    {
        $this->replyWithMessage(['text' => 'Assalomu alaykum! Botga xush kelibsiz!']);
    }
}
