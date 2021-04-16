<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\User\HiringController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    //get ->to show
    //post -> create
    // put -> update
    //delete -> delete
    //nikita
    Route::get("me", [UserController::class, 'showMe']);
    // to attach the password
    //nikita
    Route::post("me/delete", [UserController::class, 'destroy']);
    // bishish
    Route::put("me", [UserController::class, 'update']);

    Route::group(['prefix' => 'apartment'], function () {
        //bishish
        Route::post("", [ApartmentController::class, 'store']);
        //nikita
        Route::post("index", [ApartmentController::class, 'index']);
        // nikita
        Route::post("index/my", [ApartmentController::class, 'myApartment']);
        // Aman
        Route::post("{apartment}/image", [ApartmentController::class, 'addImage']);
        //nikita
        Route::get("{apartment}", [ApartmentController::class, 'show']);
        //bishish
        Route::put("{apartment}", [ApartmentController::class, 'update']);
        //Aman
        Route::post("{apartment_id}/feedback", [FeedbackController::class, 'store']);
        //Aman
        Route::delete("{apartment}", [ApartmentController::class, 'destroy']);
    });

    Route::group(['prefix' => 'rent'], function () {
        Route::group(['prefix' => 'user'], function () {
            // Nikita
            Route::get("", [HiringController::class, 'index']);
            //Aman
            Route::post("request", [HiringController::class, 'requestRent']);
            //Bishish
            Route::post("accept/{rent}", [HiringController::class, 'accept']);
            //Bishish
            Route::post("cancel/{rent}", [HiringController::class, 'cancel']);
            Route::post("pay/{rent}", [HiringController::class, 'pay']);
        });
        Route::group(['prefix' => 'provider'], function () {
            // Nikita
            Route::get("", [\App\Http\Controllers\Provider\HiringController::class, 'index']);
            //nikita
            Route::get("{rent}", [\App\Http\Controllers\Provider\HiringController::class, 'show']);
            //Aman
            Route::post("{rent}", [\App\Http\Controllers\Provider\HiringController::class, 'respondToRent']);
            //Bishish
            Route::post("complete/{rent}", [\App\Http\Controllers\Provider\HiringController::class, 'markCompleted']);
            //Bishish
            Route::post("accept/{rent}", [\App\Http\Controllers\Provider\HiringController::class, 'accept']);
            //Bishish
            Route::post("cancel/{rent}", [\App\Http\Controllers\Provider\HiringController::class, 'cancel']);
        });
    });
});

// aman
Route::post('login', [LoginController::class, 'login']);
// bishish
Route::post('signup', [SignupController::class, 'signup']);
