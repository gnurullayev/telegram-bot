<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\HomeService;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    public function __construct(
        private HomeService $homeService,
    ) {}


    /**
     * Home info
     */

    public function index()
    {
        $data = $this->homeService->index();
        return Response::customJson($data);
    }
}
