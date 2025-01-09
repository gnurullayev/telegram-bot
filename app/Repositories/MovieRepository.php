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
        $query = Movie::query()->with(["qualities"]);

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
                ->orWhere('type', 'LIKE', "%{$searchQuery}%")
                ->orWhereHas('region', function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%{$searchQuery}%");
                })
                ->orWhereHas('category', function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%{$searchQuery}%");
                });
        })->with(['regions', 'category'])
            ->paginate(10)
            ->through(function ($movie) {
                return [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'duration' => $movie->duration,
                    'description' => $movie->description,
                    'type' => $movie->type,
                    'poster_url' => asset('storage/' . $movie->poster_url),
                    'views' => $movie->views
                ];
            });

        return $movies;
    }

    /**
     * @param  int $id
     * @param  string $id
     * @return LengthAwarePaginator<int, Movie>
     */
    public function publicMovieById(int $id, string $key): array
    {

        $otherSerials = null;
        $otherCategoryMovies = null;
        $serialsParts = null;


        $movieDetail = Movie::query()->where('id',  operator: $id)->with(['category.movies', 'region'])->get()->firstOrFail();

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
                        'description' => $item->description,
                        'type' => $item->type,
                        'poster_url' => asset('storage/' . $item->poster_url),
                        'views' => $item->views,
                        'release_date' => $item->release_date
                    ];
                });
        }


        // return ($movieDetail);

        $otherMovies =   $otherSerials ?  $otherSerials : $otherCategoryMovies;
        return [
            'id' => $movieDetail->id,
            'title' => $movieDetail->title,
            'release_date' => $movieDetail->release_date,
            'duration' => $movieDetail->duration,
            'short_content' => $movieDetail->short_content,
            'description' => $movieDetail->description,
            'poster_url' =>  asset('storage/' . $movieDetail->poster_url),
            'genre' => $movieDetail->genre,
            'views' => $movieDetail->views,
            'type' => $movieDetail->type,
            'region_id' => $movieDetail->region_id,
            'region_name' => $movieDetail->region->name,
            'category_id' => $movieDetail->category_id,
            'other_movies' => $otherMovies,

        ];
    }

    /**
     * @return Collection<int, Movie>
     */
    public function topMovies(): Collection
    {
        $movies = Movie::query()
            ->where('type', MovieTypeEnum::MOVIE->value)
            ->active()
            ->with(['country'])
            ->orderBy('views', 'desc')
            ->get()
            ->take(6);

        return $movies;
    }

    /**
     * @param  int $id
     * @return Category
     */
    public function movieCategory(int $id)
    {
        $movieCategory = Category::query()->where('id', $id)->with(['movies'])->first();

        return $movieCategory;
    }
}
