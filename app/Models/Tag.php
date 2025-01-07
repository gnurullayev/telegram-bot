<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\SlugOptions;

class Tag extends Model
{

    protected $table = "tags";

    protected $fillable = [
        'name',
        'is_active',
        'slug'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name', 'id'])
            ->saveSlugsTo('slug');
    }


    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(
            Movie::class,
            'movies_tags',
            'movie_id',
            'tag_id'
        );
    }
}
