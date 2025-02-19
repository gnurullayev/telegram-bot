<?php

namespace App\Http\Controllers;

use App\Enums\MovieTypeEnum;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\Movie;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $params = collect($request->only(['pi', 'ps', 's', 'ot']));

        $params = $params->merge([
            'pi' => $params->get('pi', 1),
            'ps' => $params->get('ps', 10),
            's' => $params->get('s', ''),
            'ot' => $params->get('ot', 'desc'),
        ]);

        return $this->categoryService->index($params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request): mixed
    {
        return $this->categoryService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): mixed
    {

        return $this->categoryService->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request)
    {
        return $this->categoryService->update($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): mixed
    {
        return $this->categoryService->destroy($id);
    }

    public function categoriesForSelect(): mixed
    {
        $moviesCategories = Category::query()->active()->get();


        $moviesCategoriesMapping = $moviesCategories->map(function ($item) {
            return [
                'value' => (string) $item->id,
                'label' => $item->name,
                'type' => MovieTypeEnum::MOVIE->value,
            ];
        });



        return Response::customJson($moviesCategoriesMapping);
    }

    public function usedCategories()
    {
        $categories = $this->categoryService->usedCategories();
        return Response::customJson($categories);
    }
    public function moviesByCategory(string $slug)
    {
        $categories = $this->categoryService->moviesByCategory($slug);
        return Response::customJson($categories);
    }
}
