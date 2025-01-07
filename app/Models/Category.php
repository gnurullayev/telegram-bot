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


    public function newEloquentBuilder($query)
    {
        return new CategoryBuilder($query);
    }


    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class, 'category_id');
    }
}
