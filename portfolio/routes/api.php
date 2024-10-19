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

//Route::post('/auth/login', [\App\Http\Controllers\Auth\AuthController::class, 'loginUser']);
//Route::post('/auth/register', [\App\Http\Controllers\Auth\AuthController::class, 'register']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// frontend data
Route::get('/getPhotoData', [\App\Http\Controllers\Front\HomePageController::class, 'getImages']);
