<?php

namespace App\Repositories;

use App\Enums\MovieTypeEnum;
use App\Models\Category;
use App\Models\Movie;
use App\Models\Series;
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
        $query = Movie::query();

        if ($params->get('s')) {
            $query->where('title', 'LIKE', "%{$params->get('s')}%");
        }

        $query->orderBy('title', $params->get('ot'));
        $query->orderBy('created_at', $params->get('ot'));

        return $query;
    }

    /**
     * @param  Collection $searchQuery
     * @return LengthAwarePaginator<int, Movie>
     */
    public function searchMovie($searchQuery): LengthAwarePaginator
    {

        $movies = Movie::where(function ($query) use ($searchQuery) {
            $query->where('title', 'LIKE', "%{$searchQuery}%")
                ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                ->orWhere('rating', 'LIKE', "%{$searchQuery}%")
                ->orWhereHas('country', function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%{$searchQuery}%");
                })
                ->orWhereHas('category', function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%{$searchQuery}%");
                });
        })->with(['country', 'category'])
            ->paginate(10)
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


        $movieDetail = Movie::query()->where('slug',  operator: $slug)->with(['category.movies', 'country'])->get()->firstOrFail();

        $movieDetail->views += 1;
        $movieDetail->save();

        if ($movieDetail->category) {
            $otherCategoryMovies = $movieDetail->category->movies()
                ->where('id', '!=', $movieDetail->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'duration' => $item->duration,
                        'poster_url' => asset('storage/' . $item->poster_url),
                        'views' => $item->views,
                        'slug' => $item->slug,
                        'release_date' => $item->release_date
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
            'poster_url' =>  asset('storage/' . $movieDetail->poster_url),
            'views' => $movieDetail->views,
            'category_id' => $movieDetail->category_id,
            'other_movies' => $otherCategoryMovies,

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
            ->orderBy('views', 'desc')
            ->paginate(20);

        // Har bir filmni formatlash uchun map ishlatamiz
        $movies->getCollection()->transform(function ($movie) {
            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'duration' => $movie->duration,
                'poster_url' =>  asset('storage/' . $movie->poster_url),
                'views' => $movie->views,
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
