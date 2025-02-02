<?php

namespace App\Http\Controllers;

use App\Models\BotUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BotUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = collect($request->only(['pi', 'ps', 's', 'ot']));

        $params = $params->merge([
            'pi' => $params->get('pi', 1),
            'ps' => $params->get('ps', 10),
            's' => $params->get('s', ''),
            'ot' => $params->get('ot', 'desc'),
        ]);

        $query = BotUser::query();

        if ($params->get('s')) {
            $query->where('first_name', 'LIKE', "%{$params->get('s')}%");
        }

        $query->orderBy('created_at', $params->get('ot'));

        $totalItems = $query->count();

        $categories = $query->skip(($params->get('pi') - 1) * $params->get('ps'))
            ->take($params->get('ps'))
            ->get();
        return Response::customJson([
            'totalItems' => $totalItems,
            'currentPage' => $params->get('pi'),
            'totalPages' => ceil($totalItems / $params->get('ps')),
            'pageSize' => $params->get('ps'),
            'items' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
