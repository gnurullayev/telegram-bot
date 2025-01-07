<?php

namespace App\Models;

use App\Models\Builders\MovieBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * App\Models\Post\Post
 *
 * @property int $id
 * @property int|null $release_date
 * @property int|null $duration
 * @property int|null $rating
 * @property string $description
 * @property string $title
 * @property string $type
 * @property string $poster_url
 * @property string $video_url
 * @property int|null $country_id
 * @property int|null $category_id
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Movie query()
 * @method static MovieBuilder|Movie newModelQuery()
 * @method static MovieBuilder|Movie newQuery()
 */

class Movie extends Model
{
    use HasFactory, HasSlug;

    protected $table = "movies";

    protected $fillable = [
        'title',
        'release_date',
        'duration',
        'description',
        'short_content',
        'rating',
        'type',
        'poster_url',
        'video_url',
        'country_id',
        'series_id',
        'category_id',
        'movie_part_number',
        'genre',
        'is_active',
        'views',
        'slug'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['title', 'id'])
            ->saveSlugsTo('slug');
    }

    public function newEloquentBuilder($query)
    {
        return new MovieBuilder($query);
    }


    public function country(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'movies_tags',
            'movie_id',
            'tag_id'
        );
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(
            Genre::class,
            'movies_tags',
            'movie_id',
            'genre_id'
        );
    }
}
