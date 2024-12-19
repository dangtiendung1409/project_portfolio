<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomePageController;
use App\Http\Controllers\Auth\AuthUserController;
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

// frontend data
Route::post('/login', [AuthUserController::class, 'login']);
Route::post('/register', [AuthUserController::class, 'register']);
Route::post('/refresh-token', [AuthUserController::class, 'refreshToken']);
Route::post('/logout', [AuthUserController::class, 'logout']);

Route::get('/getPhotoData', [HomePageController::class, 'getImages']);
Route::get('/getFollowData', [HomePageController::class, 'getFollows']);
Route::get('/getPhotoDetail/{token}', [HomePageController::class, 'getPhotoDetail']);


Route::middleware('auth:api')->group(function () {
    Route::post('/update-profile', [HomePageController::class, 'updateProfile']);
    Route::get('/user', [AuthUserController::class, 'getUser']);
    Route::get('/liked-photos', [HomePageController::class, 'getLikedPhotos']);
});
