<?php

namespace App\Services;

class HomeService
{
    public function __construct(
        private MovieService $movieService,
        private CategoryService $categoryService,
    ) {}


    /**
     * Summary of index
     * @return void
     */
    public function index(): array
    {

        $data = [
            'categories' => $this->categoryService->usedCategories()->toArray(),
            'top_movies' => collect(array_merge(
                $this->movieService->topMovies()->toArray(),
            ))->sortByDesc('views')->values()->all(),
        ];

        return $data;
    }
}
