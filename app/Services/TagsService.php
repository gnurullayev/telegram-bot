<?php

namespace App\Services;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Requests\TagStoreRequest;
use App\Http\Requests\TagUpdateRequest;
use App\Models\Category;
use App\Models\Movie;
use App\Models\Sitemap;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TagsService
{

    /**
     * Display a listing of the resource.
     */
    public function index(Collection $params): JsonResponse
    {
        $query = Tag::query();

        if ($params->get('s')) {
            $query->where('name', 'LIKE', "%{$params->get('s')}%");
        }

        $query->orderBy('created_at', $params->get('ot'));

        $totalItems = $query->count();
        $tags = $query->skip(($params->get('pi') - 1) * $params->get('ps'))
            ->take($params->get('ps'))
            ->get();

        return Response::customJson([
            'totalItems' => $totalItems,
            'currentPage' => $params->get('pi'),
            'totalPages' => ceil($totalItems / $params->get('ps')),
            'pageSize' => $params->get('ps'),
            'items' => $tags
        ]);
    }

    /**
     * Store a newly created series in storage.
     */
    public function store(TagStoreRequest $request): mixed
    {

        try {
            $validated = $request->validated();

            $tag = Tag::create($validated);

            return Response::customJson([
                'id' => $tag->id,
                'name' => $tag->name,
            ], 201);
        } catch (\Exception $e) {
            return Response::customJsonError('Failed to create category' . " " . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified movie.
     */
    public function show($id): mixed
    {

        try {
            $tag = Tag::findOrFail($id);
            return Response::customJson($tag);
        } catch (\Exception $e) {
            return Response::customJsonError('Movie not found' . " " . $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified series in storage.
     */
    public function update(TagUpdateRequest $request, $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $validated = $request->validated();
            $sitemap = Sitemap::where('url', $validated['link'])->first();

            if ($sitemap) {
                $sitemap->update([
                    'url' => $validated['link'],
                    'lastmod' => $tag->updated_at,
                ]);
            } else {
                Sitemap::create([
                    'url' => $validated['link'],
                    'lastmod' => $tag->created_at,
                    'changefreq' => "weekly",
                    'priority' => "0.9",
                ]);
            }
            $tag->update($validated);

            return Response::customJson($tag);
        } catch (\Exception $e) {
            return Response::customJsonError('Failed to update category' . " " . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified series from storage.
     */
    public function destroy($id): mixed
    {
        try {
            $tag = Tag::findOrFail($id);

            $tag->delete();

            return Response::customJson(['message' => 'Series deleted successfully']);
        } catch (\Exception $e) {
            return Response::customJsonError('Failed to delete tag' . " " . $e->getMessage(), 500);
        }
    }


    public function usedTags()
    {
        // // Teglarni olish
        $tags = Tag::whereHas('movies')
            ->get()
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];
            });

        return $tags;
    }
    public function moviesByTag(string $slug)
    {
        $tag = Tag::query()
            ->where('slug', $slug)
            ->firstOrFail();

        /** @var LengthAwarePaginator $movies */
        $movies = $tag->movies()->paginate(20);

        $moviesMapped = $movies->getCollection()->map(function ($movie) {
            return [
                ...$movie->toArray(),
                'poster_url' => asset('storage/' . $movie->poster_url),
            ];
        });

        $movies = new LengthAwarePaginator(
            $moviesMapped,
            $movies->total(),
            $movies->perPage(),
            $movies->currentPage(),
            ['path' => $movies->path()]
        );

        return [
            'tag' => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'link' => $tag->link
            ],
            'movies' => $movies,
        ];
    }
}
