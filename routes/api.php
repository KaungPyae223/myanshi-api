<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    $response = [
        "message" => "Welcome from Myanshi App API",
        "developed_by" => "MCB-1",
        "for" => "MMS Connections Project",
    ];
    return response()->json($response);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/user/search', 'search')->name('user.search');
        Route::post('/user/{user}/update-profile-image',  'updateProfileImage')->name('user.update-profile-image');
        Route::apiResource('user', UserController::class);
    });

    Route::controller(ReviewController::class)->group(function () {
        Route::get('/review/search', 'search')->name('review.search');
        Route::post('review/{review}/update/customer-image', 'updateCustomerImage')->name('review.update.customer-image');
        Route::post('review/{review}/update/review-image', 'updateReviewImage')->name('review.update.review-image');
        Route::apiResource('review', ReviewController::class);
    });

    Route::controller(LocationController::class)->group(function () {
        Route::get('/location/search', 'search')->name('location.search');
        Route::post('location/{location}/update-image', 'updateImage')->name('location.update-image');
        Route::apiResource('location', LocationController::class);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'profile');
        Route::post('/change-password', 'changePassword');
        Route::post('/change-profile-image', 'changeProfileImage');
        Route::put('/profile', 'update');
    });
});
