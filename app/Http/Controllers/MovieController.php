<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieStoreRequest;
use App\Http\Requests\MovieUpdateRequest;
use App\Services\MovieService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MovieController extends Controller
{
    public function __construct(
        private MovieService $movieService
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

        return $this->movieService->index($params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieStoreRequest $request): mixed
    {
        return $this->movieService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): mixed
    {
        return $this->movieService->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieUpdateRequest $request)
    {
        return $this->movieService->update($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): mixed
    {
        return $this->movieService->destroy($id);
    }

    /**
     * search movie
     */
    public function search(Request $request, string $search): mixed
    {
        return $this->movieService->search($search);
    }

    /**
     * get public movie
     */
    public function movieDetail(Request $request, string $slug): mixed
    {
        return $this->movieService->movieDetail($slug);
    }
}
