<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::get('countries', [CountryController::class, "index"]);
    Route::get('all-categories', [CategoryController::class, "categoriesForSelect"]);
    Route::get('all-tags', [TagController::class, "tagsForSelect"]);
    Route::get('all-genres', [GenreController::class, "genresForSelect"]);

    Route::get('used-categories', [CategoryController::class, "usedCategories"]);
    Route::get('movies-search', [MovieController::class, "search"]);
    Route::get('home', [HomeController::class, "index"]);
    Route::get('all-movies/{id}', [MovieController::class, "moviesByCategory"]);
    Route::get('movie-detail/{id}/{key}', [MovieController::class, "movieDetail"]);

    Route::post('/upload', [FileUploadController::class, 'upload']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::post('movie-upload', [MovieController::class, 'movieUpload']);
        Route::post('movies/update', [MovieController::class, 'update']);
        Route::apiResource('movies', MovieController::class);

        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('tags', TagController::class);
        Route::apiResource('genres', GenreController::class);
    });
});
