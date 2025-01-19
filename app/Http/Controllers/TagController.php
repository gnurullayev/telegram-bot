<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Tag;
use App\Services\TagsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TagController extends Controller
{
    public function __construct(
        private TagsService $tagService
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

        return $this->tagService->index($params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request): mixed
    {
        return $this->tagService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): mixed
    {

        return $this->tagService->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, string $id)
    {
        return $this->tagService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): mixed
    {
        return $this->tagService->destroy($id);
    }

    public function tagsForSelect(): mixed
    {
        $moviesCategories = Tag::query()
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

    public function usedTags()
    {
        $tags = $this->tagService->usedTags();
        return Response::customJson($tags);
    }

    public function moviesByTag(string $slug)
    {
        $tags = $this->tagService->moviesByTag($slug);
        return Response::customJson($tags);
    }
}
