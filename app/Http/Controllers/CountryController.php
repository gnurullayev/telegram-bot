<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\Response;

class CountryController extends Controller
{

    public function index()
    {
        $countries = Country::all();
        $countriesMapping = $countries->map(function ($item) {
            return [
                'value' => $item->id . "",
                'label' => $item->name,
            ];
        });

        return Response::customJson($countriesMapping);
    }
}
