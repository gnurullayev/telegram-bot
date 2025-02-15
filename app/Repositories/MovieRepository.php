<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MovieRepository extends BaseRepository
{

    /**
     * @return class-string<Movie>
     */
    public static function modelClass(): string
    {
        return Movie::class;
    }

    /**
     *
     * @return Collection<int, Movie>
     */
    public function ActiveMovie(): Collection
    {
        return Movie::query()->active()->get();
    }

    /**
     * @param  Collection $params
     * @return Collection<int, Movie>
     */
    public function allMovies($params)
    {
        $query = Movie::query()->with('movieCode');

        if ($params->get('s')) {
            $query->where('title', 'LIKE', "%{$params->get('s')}%");
        }

        $query->orderBy('created_at', $params->get('ot'));

        return $query;
    }

    /**
     * @param  Collection $searchQuery
     * @return LengthAwarePaginator<int, Movie>
     * @param string $search
     */
    public function searchMovie($search): LengthAwarePaginator
    {

        $movies = Movie::where(function ($query) use ($search) {
            $query->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
        })->with(['category'])
            ->paginate(20)
            ->through(function ($movie) {
                return [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'duration' => $movie->duration,
                    'description' => $movie->description,
                    'poster_url' => asset('storage/' . $movie->poster_url),
                    'views' => $movie->views,
                    'slug' => $movie->slug,

                ];
            });

        return $movies;
    }

    /**
     * @param  string $slug
     * @return LengthAwarePaginator<int, Movie>
     */
    public function publicMovieById(string $slug): array
    {

        $otherCategoryMovies = null;


        $movieDetail = Movie::query()->where('slug',  operator: $slug)->with(['category.movies', 'country', "movieCode", 'tags'])->get()->firstOrFail();

        $movieDetail->views += 1;
        $movieDetail->save();

        if ($movieDetail->category) {
            $otherCategoryMovies = $movieDetail->category->movies()
                ->where('id', '!=', $movieDetail->id)
                ->orderBy('created_at', "desc")
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'duration' => $item->duration,
                        'poster_url' => asset('storage/' . $item->poster_url),
                        'views' => $item->views,
                        'slug' => $item->slug,
                        'release_date' => $item->release_date,
                        'keyworda' => $item->keyworda,
                    ];
                });
        }

        if ($movieDetail->tags) {
            $movieDetail->tags_data = $movieDetail->tags->map(function ($tag) {
                return [
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'id' => $tag->id,
                ];
            });
        }

        return [
            'id' => $movieDetail->id,
            'title' => $movieDetail->title,
            'release_date' => $movieDetail->release_date,
            'duration' => $movieDetail->duration,
            'short_content' => $movieDetail->short_content,
            'description' => $movieDetail->description,
            'video_url' => $movieDetail->video_url,
            'poster_url' =>  asset('storage/' . $movieDetail->poster_url),
            'views' => $movieDetail->views,
            'category_id' => $movieDetail->category_id,
            'category_name' => $movieDetail->category_name,
            'link' => $movieDetail->movieCode ? $movieDetail->movieCode->link : null,
            'country_id' => $movieDetail->country_id,
            'country_name' => $movieDetail->country_name,
            'keyworda' => $movieDetail->keyworda,
            'other_movies' => $otherCategoryMovies,
            'tags_data' => $movieDetail->tags_data
        ];
    }

    /**
     * @return LengthAwarePaginator<int, Movie>
     */
    public function topMovies(): LengthAwarePaginator
    {
        $movies = Movie::query()
            ->active()
            ->with(['country'])
            // ->orderBy('views', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Har bir filmni formatlash uchun map ishlatamiz
        $movies->getCollection()->transform(function ($movie) {
            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'duration' => $movie->duration,
                'poster_url' =>  asset('storage/' . $movie->poster_url),
                'views' => $movie->views,
                'slug' => $movie->slug,
                'keyworda' => $movie->keyworda,
                'category_id' => $movie->category_id,
            ];
        });

        return $movies;
    }

    /**
     * @param  int $id
     * @return Category
     */
    public function movieCategory(int $id)
    {
        $movieCategory = Category::query()
            ->where('id', $id)
            ->with(['movies'])
            ->first();

        if ($movieCategory) {
            // Movies kolleksiyasini map yordamida qayta ishlash
            $movieCategory->movies = $movieCategory->movies->map(function ($movie) {
                return [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'views' => $movie->views,
                    'duration' => $movie->duration,
                    'poster_url' => $movie->poster_url ? asset('storage/' . $movie->poster_url) : null,
                ];
            });
        }

        return $movieCategory;
    }
}
