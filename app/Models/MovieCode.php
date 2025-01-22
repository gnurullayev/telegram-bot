<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $link
 * @property int|null $movie_id
 * @property int|null $series_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

class MovieCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'movie_id',
        'series_id'
    ];

    protected $table = "movie_codes";

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
