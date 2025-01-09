<?php


namespace App\Services;

use App\Enums\MovieTypeEnum;
use App\Http\Requests\MovieStoreRequest;
use App\Http\Requests\MovieUpdateRequest;
use App\Models\Movie;
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
                'poster_url' => asset('storage/' . $item->poster_url),
                'country_id' => $item->country_id,
                'is_active' => $item->is_active,
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
            $movie = Movie::findOrFail($id);
            return Response::customJson($movie);
        } catch (\Exception $e) {
            return Response::customJsonError('Movie not found', 404);
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
            return Response::customJson($movie->load("category"), 201);
        } catch (\Exception $e) {
            return Response::customJsonError('Failed to create movie', 500);
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

            $movie->update($validated);

            return Response::customJson($movie);
        } catch (\Exception $e) {
            return Response::customJsonError('Failed to update movie', 500);
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
            return Response::customJsonError('Failed to delete movie', 500);
        }
    }

    public function search(Request $request)
    {
        $searchQuery = $request->input('query');

        $movies = $this->movieRepository->searchMovie($searchQuery);
        return  Response::customJson($movies, 200);
    }

    public function movieDetail(int $id, string $key)
    {

        $movie = $this->movieRepository->publicMovieById($id, $key);

        return  Response::customJson($movie, 200);
    }

    public function topMovies()
    {
        $movies = $this->movieRepository->topMovies();
        $moviesMapping = $movies->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'duration' => $item->duration,
                'description' => $item->description,
                'type' => $item->type,
                'poster_url' => asset('storage/' . $item->poster_url),
                'views' => $item->views
            ];
        });
        return $moviesMapping;
    }

    /**
     * @param  int $id
     * @param  string $key
     * @param  int $perPage
     * @param  int $page
     * @return array
     */
    public function moviesByCategory(int $id, string $key, int $perPage = 10)
    {
        $category = $this->movieRepository->movieCategory($id);

        if ($key === MovieTypeEnum::MOVIE->value) {
            $moviesData = $category->movies()->orderBy('created_at', 'desc')->paginate($perPage);
        } else {
            $moviesData = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }

        // `data` maydonidagi ma'lumotlarni xaritada qayta ishlash
        $moviesData->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'type' => $item->type,
                'poster_url' => asset('storage/' . $item->poster_url),
                'views' => $item->views,
            ];
        });

        return [
            "id" => $category->id,
            "name" => $category->name,
            "short_content" => $category->short_content,
            "description" => $category->description,
            'movies_data' => $moviesData,
        ];
    }
}
