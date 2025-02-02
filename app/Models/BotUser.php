<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotUser extends Model
{
    use HasFactory;

    protected $table = 'bot_users'; // Jadval nomi
    protected $fillable = [
        'telegram_id',
        'first_name',
        'last_name',
        'username',
    ];
}
