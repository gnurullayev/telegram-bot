<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Genre;
use App\Services\GenresService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class GenreController extends Controller
{
    public function __construct(
        private GenresService $genreService
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

        return $this->genreService->index($params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request): mixed
    {
        return $this->genreService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): mixed
    {

        return $this->genreService->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, string $id)
    {
        return $this->genreService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): mixed
    {
        return $this->genreService->destroy($id);
    }


    public function genresForSelect(): mixed
    {
        $moviesCategories = Genre::query()
            ->isActive()
            ->get();


        $moviesCategoriesMapping = $moviesCategories->map(function ($item) {
            return [
                'value' => (string) $item->id,
                'label' => $item->name,
            ];
        });



        return Response::customJson($moviesCategoriesMapping);
    }

    // public function usedCategories()
    // {
    //     $categories = $this->genreService->usedCategories();
    //     return Response::customJson($categories);
    // }
}
