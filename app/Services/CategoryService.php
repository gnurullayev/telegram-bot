<?php

namespace App\Services;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\Movie;
use App\Models\Sitemap;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CategoryService
{

    /**
     * Display a listing of the resource.
     */
    public function index(Collection $params): JsonResponse
    {
        $query = Category::query();

        if ($params->get('s')) {
            $query->where('name', 'LIKE', "%{$params->get('s')}%");
        }

        $query->orderBy('name', $params->get('ot'));
        $query->orderBy('created_at', $params->get('ot'));

        $totalItems = $query->count();
        // $categories = $query->skip(($params->get('pi') - 1) * $params->get('ps'))
        //     ->take($params->get('ps'))
        //     ->get();

        $categories = $query->skip(($params->get('pi') - 1) * $params->get('ps'))
            ->take($params->get('ps'))
            ->get()
            ->map(function ($category) {
                return [
                    "id" => $category->id,
                    "name" => $category->name,
                    "poster_url" => asset('storage/' . $category->poster_url),
                    "is_active" => $category->is_active,
                    "link" => $category->link,
                ];
            });
        return Response::customJson([
            'totalItems' => $totalItems,
            'currentPage' => $params->get('pi'),
            'totalPages' => ceil($totalItems / $params->get('ps')),
            'pageSize' => $params->get('ps'),
            'items' => $categories
        ]);
    }

    /**
     * Store a newly created series in storage.
     */
    public function store(CategoryStoreRequest $request): mixed
    {

        try {
            $validated = $request->validated();
            if ($request->hasFile('poster_url')) {
                $posterPath = $request->file('poster_url')->store('posters', 'public');
                $validated['poster_url'] = $posterPath;
            }

            $category = Category::create($validated);

            return Response::customJson([
                'id' => $category->id,
                'name' => $category->name,
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
            $category = Category::findOrFail($id);
            $category->poster_url = asset('storage/' . $category->poster_url);
            return Response::customJson($category);
        } catch (\Exception $e) {
            return Response::customJsonError('Movie not found' . " " . $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified series in storage.
     */
    public function update(CategoryUpdateRequest $request)
    {
        try {
            $validated = $request->validated();
            $category = Category::findOrFail($validated["id"]);

            if ($request->hasFile('poster_url')) {
                if ($category->poster_url) {
                    Storage::disk('public')->delete($category->poster_url);
                }

                $posterPath = $request->file('poster_url')->store('posters', 'public');
                $validated['poster_url'] = $posterPath;
            } else {
                $validated['poster_url'] = $category->poster_url;
            }

            $category->update($validated);

            $sitemap = Sitemap::where('url', $category->link)->first();

            if ($sitemap) {
                $sitemap->update([
                    'url' => $validated['link'],
                    'lastmod' => $category->updated_at,
                ]);
            } else {
                Sitemap::create([
                    'url' => $validated['link'],
                    'lastmod' => $category->created_at,
                    'changefreq' => "weekly",
                    'priority' => "0.9",
                ]);
            }


            return Response::customJson($category);
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
            $category = Category::findOrFail($id);

            $category->delete();

            return Response::customJson(['message' => 'Series deleted successfully']);
        } catch (\Exception $e) {
            return Response::customJsonError('Failed to delete category' . " " . $e->getMessage(), 500);
        }
    }

    public function usedCategories()
    {
        // Foydalanilgan kategoriyalarni olish (SQL darajasida unique)
        $usedCategoryIds = Movie::whereNotNull('category_id')
            ->pluck('category_id')
            ->unique();

        // Umumiy foydalanilgan kategoriyalar soni
        $totalUsedCategories = $usedCategoryIds->count();

        // Foydalanilgan kategoriyalarni paginate qilish
        $categories = Category::whereIn('id', $usedCategoryIds)
            ->paginate(20);

        // Har bir kategoriyani xaritada o‘zgartirish
        $categories->getCollection()->transform(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'poster_url' => asset('storage/' . $category->poster_url),
            ];
        });

        // Totalni to'g'irlash
        $categories->setCollection($categories->getCollection());
        $categories->total($totalUsedCategories);

        return $categories;
    }

    public function moviesByCategory(string $slug)
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->firstOrFail();

        /** @var LengthAwarePaginator $movies */
        $movies = $category->movies()->paginate(20);

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
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                "short_content" => "",
                "description" => "",
                'poster_url' => asset('storage/' . $category->poster_url),
            ],
            'movies' => $movies,
        ];
    }
}
