<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::get('countries', [CountryController::class, "index"]);
    Route::get('all-categories', [CategoryController::class, "categoriesForSelect"]);
    Route::get('all-tags', [TagController::class, "tagsForSelect"]);

    Route::get('used-categories', [CategoryController::class, "usedCategories"]);
    Route::get('used-tags', [TagController::class, "usedTags"]);
    Route::get('movies-by-category/{slug}', [CategoryController::class, "moviesByCategory"]);
    Route::get('movies-by-tag/{slug}', [TagController::class, "moviesByTag"]);
    Route::get('movies-search/{search}', [MovieController::class, "search"]);
    Route::get('home', [HomeController::class, "index"]);
    Route::get('all-movies/{id}', [MovieController::class, "moviesByCategory"]);
    Route::get('movie-detail/{slug}', [MovieController::class, "movieDetail"]);
    Route::get('sitemaps', [SitemapController::class, "index"]);
    Route::post('/upload', [FileUploadController::class, 'upload']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::post('movie-upload', [MovieController::class, 'movieUpload']);
        Route::post('movies/update', [MovieController::class, 'update']);
        Route::apiResource('movies', MovieController::class);

        Route::post('categories/update', [CategoryController::class, 'update']);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('tags', TagController::class);
    });
});
