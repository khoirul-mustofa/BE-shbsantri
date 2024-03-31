<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NewsController;
use App\Var\ApiVersion;
use Illuminate\Support\Facades\Route;


Route::prefix(ApiVersion::V1)->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('/auth/register', 'register');
        Route::post('/auth/login', 'login');
    });

    Route::controller(NewsController::class)->group(function () {
        Route::get('/news', 'index');
        Route::get('/news/{news}','show');



    });

    Route::middleware('auth:sanctum')->group(function () {
//        news
        Route::controller(NewsController::class)->group(function () {
            Route::post('/news', 'store');
            Route::put('/news/{news}', 'update');
            Route::delete('/news/{news}',  'destroy');
        });

//        auth
        Route::controller(AuthController::class)->group(function () {
            Route::post('/auth/logout', 'logout');
            Route::post('/auth/update', 'update');
        });

    });
});

