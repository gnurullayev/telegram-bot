<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Support\Facades\Response;

class CountryController extends Controller
{

    public function index()
    {
        $countries = Region::all();
        $countriesMapping = $countries->map(function ($item) {
            return [
                'value' => $item->id . "",
                'label' => $item->name,
            ];
        });

        return Response::customJson($countriesMapping);
    }
}
