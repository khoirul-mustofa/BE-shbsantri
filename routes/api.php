<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\NewsController;
use App\Models\User;
use App\Response\CustomsResponse;
use App\Var\ApiVersion;
use Illuminate\Support\Facades\Route;


Route::prefix(ApiVersion::V1)->group(function () {

    Route::get("/users", function () {
        $users = User::all();
        return CustomsResponse::success(
            $users,
            'Users retrieved successfully.',
        );
    });





    // Routes for downloading files
    Route::get('/download/pdf/{fileName}', [FileController::class, 'downloadPDF']);
    Route::get('/download/ppt/{fileName}', [FileController::class, 'downloadPPT']);
    Route::get('/download/image/{fileName}', [FileController::class, 'downloadImage']);

    Route::get('/files', [FileController::class, 'listFiles']);


    Route::controller(AuthController::class)->group(function () {
        Route::post('/auth/register', 'register');
        Route::post('/auth/login', 'login');
    });
    Route::controller(NewsController::class)->group(function () {
        Route::get('/news', 'index');
        Route::get('/news/{news}','show');
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(NewsController::class)->group(function () {
            Route::post('/news', 'store');
            Route::put('/news/{news}', 'update');
            Route::delete('/news/{news}',  'destroy');
        });
        Route::controller(AuthController::class)->group(function () {
            Route::post('/auth/logout', 'logout');
            Route::put('/auth/update', 'update');
//            Route::delete('/users/{id}', 'destroy');
        });

        // Routes for uploading files
        Route::post('/upload/image', [FileController::class, 'uploadImage']);
        Route::post('/upload/pdf', [FileController::class, 'uploadPDF']);
        Route::post('/upload/ppt', [FileController::class, 'uploadPPT']);

        Route::resource('/category', CategoryController::class);
    });


});

