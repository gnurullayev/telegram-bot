<?php

namespace App\Services;

use App\Models\Movie;

class HomeService
{
    public function __construct(
        private MovieService $movieService,
        private CategoryService $categoryService,
        private TagsService $tagsService,
    ) {}


    /**
     * Summary of index
     * @return void
     */
    public function index(): array
    {
        $categories = Movie::whereNotNull('category_id')
            ->with('category')
            ->get()
            ->pluck('category')
            ->unique('id')
            ->take(10)
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'poster_url' =>  asset('storage/' . $category->poster_url),
                    'slug' => $category->slug
                ];
            })->values();

        $data = [
            'categories' => $categories,
            'top_movies' => $this->movieService->topMovies(),
            'tags' => $this->tagsService->usedTags()->toArray(),
        ];

        return $data;
    }
}
