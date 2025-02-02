<?php


namespace App\Services;

use App\Http\Requests\MovieStoreRequest;
use App\Http\Requests\MovieUpdateRequest;
use App\Models\Movie;
use App\Models\MovieCode;
use App\Models\Sitemap;
use App\Repositories\MovieRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class MovieService
{
    public function __construct(
        private MovieRepository $movieRepository
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Collection $params): JsonResponse
    {
        $query = $this->movieRepository->allMovies($params);

        $totalItems = $query->count();
        $movies = $query->skip(($params->get('pi') - 1) * $params->get('ps'))
            ->take($params->get('ps'))
            ->get();

        $countriesMapping = $movies->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'release_date' => $item->release_date,
                'duration' => $item->duration,
                'description' => $item->description,
                'rating' => $item->rating,
                'type' => $item->type,

                'views' => $item->views,
                'poster_url' => asset('storage/' . $item->poster_url),
                'country_id' => $item->country_id,
                'video_url' => $item->video_url,
                'is_active' => $item->is_active,
                'keywords' => $item->keywords,
                'movieCode' => $item->movieCode ? [
                    'id' => $item->movieCode['id'],
                    'link' => $item->movieCode->link,
                    'movie_id' => $item->movieCode->movie_id
                ] : null
            ];
        });

        return Response::customJson([
            'totalItems' => $totalItems,
            'currentPage' => $params->get('pi'),
            'totalPages' => ceil($totalItems / $params->get('ps')),
            'pageSize' => $params->get('ps'),
            'items' => $countriesMapping
        ]);
    }

    /**
     * Display the specified movie.
     */
    public function show($id)
    {
        try {
            $movie = Movie::query()->where("id", $id)->with(['tags:id', "movieCode"])->get()->first();
            // Custom tags_data uchun
            $tagsData = [];
            foreach ($movie->tags as $tag) {
                $tagsData[] = $tag->id;
            }

            // Qo'shimcha ma'lumotni $movie modeliga qo'shish
            $movie->setAttribute('tags_data', $tagsData);
            $movie->setAttribute('link', $movie->movieCode ? $movie->movieCode->link : null);
            return Response::customJson($movie);
        } catch (\Exception $e) {
            return Response::customJsonError('Movie not found' . " " . $e->getMessage(), 404);
        }
    }

    /**
     * Store a newly created movie in storage.
     */
    public function store(MovieStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('poster_url')) {
                $posterPath = $request->file('poster_url')->store('posters', 'public');
                $validated['poster_url'] = $posterPath;
            }

            $movie = Movie::create($validated);
            if (!empty($validated["tags"])) {
                foreach ($validated["tags"] as $tag) {
                    $movie->tags()->attach($tag);
                }
            }

            return Response::customJson($movie->load("category"), 201);
        } catch (\Exception $e) {
            return Response::customJsonError('Failed to create movie: ' . " " . $e->getMessage(), 500);
        }
    }


    /**
     * Update the specified movie in storage.
     */
    public function update(MovieUpdateRequest $request)
    {
        try {
            $validated = $request->validated();

            $movie = Movie::findOrFail($validated['id']);


            if ($request->hasFile('poster_url')) {
                if ($movie->poster_url) {
                    Storage::disk('public')->delete($movie->poster_url);
                }

                $posterPath = $request->file('poster_url')->store('posters', 'public');
                $validated['poster_url'] = $posterPath;
            }

            if (!empty($validated["tags"])) {
                $movie->tags()->syncWithoutDetaching($validated["tags"]);
            }

            $movieCode = MovieCode::where('movie_id', $movie->id)->first();

            if ($movieCode) {
                $movieCode->update([
                    'link' => $validated['link'],
                ]);
            } else {
                $movieCode = MovieCode::create([
                    'link' => $validated['link']
                ]);
            }


            $sitemap = Sitemap::where('url', $movieCode->link)->first();
            if ($sitemap) {
                $sitemap->update([
                    'url' => $movieCode->link,
                    'lastmod' => $movieCode->updated_at,
                ]);
            } else {
                Sitemap::create([
                    'url' => $movieCode->link,
                    'lastmod' => $movieCode->created_at,
                    'changefreq' => "weekly",
                    'priority' => "0.9",
                ]);
            }

            $movie->update($validated);

            return Response::customJson($movie);
        } catch (\Exception $e) {
            return Response::customJsonError('Failed to update movie' . " " . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified movie from storage.
     */
    public function destroy($id)
    {
        try {
            $movie = Movie::findOrFail($id);

            if ($movie->poster_url) {
                Storage::disk('public')->delete($movie->poster_url);
            }

            $movie->delete();

            return Response::customJson(['message' => 'Movie deleted successfully']);
        } catch (\Exception $e) {
            return Response::customJsonError('Failed to delete movie' . " " . $e->getMessage(), 500);
        }
    }

    public function search(string $search)
    {
        $movies = $this->movieRepository->searchMovie($search);
        return  Response::customJson($movies, 200);
    }

    public function movieDetail(string $slug)
    {

        $movie = $this->movieRepository->publicMovieById($slug);

        return  Response::customJson($movie, 200);
    }

    public function topMovies()
    {
        $movies = $this->movieRepository->topMovies();
        return $movies;
    }
}
