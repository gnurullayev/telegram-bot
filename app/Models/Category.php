<?php

namespace App\Models;

use App\Models\Builders\CategoryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasFactory, HasSlug;

    protected $table = "categories";

    protected $fillable = [
        'name',
        'is_active',
        'short_content',
        'description',
        'poster_url',
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

    /**
     * Scope a query to only include active tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }



    public function newEloquentBuilder($query)
    {
        return new CategoryBuilder($query);
    }


    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class, 'category_id');
    }
}
