<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// frontend data
Route::post('/login', [\App\Http\Controllers\Auth\AuthUserController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\Auth\AuthUserController::class, 'register']);
Route::post('/logout', [\App\Http\Controllers\Auth\AuthUserController::class, 'logout']);
Route::get('/getPhotoData', [\App\Http\Controllers\Front\HomePageController::class, 'getImages']);
Route::get('/getFollowData', [\App\Http\Controllers\Front\HomePageController::class, 'getFollows']);

