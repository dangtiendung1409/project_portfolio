<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomePageController;
use App\Http\Controllers\Auth\AuthUserController;
use App\Http\Controllers\Front\AccountUserController;
use App\Http\Controllers\Front\PhotoDetailController;
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
Route::get('/getPhotoDetail/{token}', [PhotoDetailController::class, 'getPhotoDetail']);


Route::middleware('auth:api')->group(function () {
    // Acount user
    Route::post('/update-profile', [AccountUserController::class, 'updateProfile']);
    Route::post('/change-password', [AccountUserController::class, 'changePassword']);
    Route::get('/liked-photos', [AccountUserController::class, 'getLikedPhotos']);
    Route::post('/add-gallery', [AccountUserController::class, 'addGallery']);
    Route::get('/galleries', [AccountUserController::class, 'getAllGalleries']);
    Route::get('/gallery-details/{galleries_code}', [AccountUserController::class, 'getGalleryDetails']);

    // Home Page
    Route::post('/like-photo', [HomePageController::class, 'likePhoto']);
    Route::post('/unlike-photo', [HomePageController::class, 'unlikePhoto']);
    Route::get('/notifications', [HomePageController::class, 'getUserNotifications']);
    Route::post('/notifications/mark-as-read', [HomePageController::class, 'markNotificationAsRead']);

    // Auth user
    Route::get('/user', [AuthUserController::class, 'getUser']);
});
