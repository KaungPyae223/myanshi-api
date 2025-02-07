<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');







Route::prefix("v1")->group(function () {
    Route::apiResource('faq', FaqController::class);

    Route::post('gallery/update-image/{id}', [GalleryController::class, 'updateGalleryImage']);

    Route::apiResource('gallery', GalleryController::class);


    Route::post('author/update-image/{id}', [AuthorController::class, 'updateAuthorImage']);
    Route::apiResource('author', AuthorController::class);

    Route::post('blog/update-image/{id}', [BlogController::class, 'updatBlogImage']);
    Route::apiResource('blog', BlogController::class);
});
