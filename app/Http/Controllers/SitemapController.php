<?php

namespace App\Http\Controllers;

use App\Models\Sitemap;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{

    /**
     * Sitemap info
     */

    public function index()
    {
        $data = Sitemap::all();
        return Response::customJson($data);
    }
}
