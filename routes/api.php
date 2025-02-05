<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PromotionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix("v1")->group(function () {

    Route::post("menu/image-update/{id}",[MenuController::class,"updateImage"]);
    Route::apiResource("menu",MenuController::class);
    Route::apiResource("category",CategoryController::class);
    Route::put("create-promotion/{id}",[PromotionController::class,"createPromotion"]);
    Route::get("promotion",[PromotionController::class,"index"]);

});
