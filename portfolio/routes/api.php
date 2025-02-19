<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomePageController;
use App\Http\Controllers\Auth\AuthUserController;
use App\Http\Controllers\Front\AccountUserController;
use App\Http\Controllers\Front\PhotoDetailController;
use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\SearchController;
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

// home page
Route::get('/getPhotoData', [HomePageController::class, 'getImages']);
Route::get('/getFollowData', [HomePageController::class, 'getFollows']);
Route::get('/getPhotoDetail/{token}', [PhotoDetailController::class, 'getPhotoDetail']);
Route::get('/comments/{token}', [PhotoDetailController::class, 'getCommentsByPhotoToken']);
Route::get('/categories', [HomePageController::class, 'getAllCategories']);
Route::get('/tags', [HomePageController::class, 'getAllTags']);

//Profile user
Route::get('/user-by-username/{username}', [ProfileController::class, 'getUserByUserName']);
Route::get('/photos-by-username/{username}', [ProfileController::class, 'getPhotosByUserName']);
Route::get('/galleries-by-username/{username}', [ProfileController::class, 'getGalleriesByUserName']);

//Category
Route::get('/categories/photos', [CategoryController::class, 'getPhotosByCategorySlugs']);

// Search photos
Route::get('/search-photos', [SearchController::class, 'searchPhotos']);

Route::middleware('auth:api')->group(function () {
    // my photo user
    Route::get('/approved-photos', [AccountUserController::class, 'getApprovedPhotos']);
    Route::delete('/photos/{photo_id}', [AccountUserController::class, 'deletePhoto']);
    // edit photo
    Route::get('/photo/{id}', [AccountUserController::class, 'getPhoto']);
    Route::put('/edit-photo/{id}', [AccountUserController::class, 'editPhoto']);

    // Acount user
    Route::post('/update-profile', [AccountUserController::class, 'updateProfile']);
    Route::post('/change-password', [AccountUserController::class, 'changePassword']);

    // like
    Route::get('/liked-photos', [AccountUserController::class, 'getLikedPhotos']);
    Route::delete('/like/{photo_id}', [AccountUserController::class, 'deleteLike']);
    Route::post('/like-photo', [HomePageController::class, 'likePhoto']);
    Route::post('/unlike-photo', [HomePageController::class, 'unlikePhoto']);

    // gallery
    Route::post('/add-gallery', [AccountUserController::class, 'addGallery']);
    Route::post('/update-gallery/{galleries_code}', [AccountUserController::class, 'updateGallery']);
    Route::delete('/delete-gallery/{galleries_code}', [AccountUserController::class, 'deleteGallery']);

    Route::get('/galleries', [AccountUserController::class, 'getAllGalleries']);
    Route::post('/gallery/add-photo', [AccountUserController::class, 'addPhotoToGallery']);
    Route::get('/gallery-details/{galleries_code}', [AccountUserController::class, 'getGalleryDetails']);
    Route::delete('/gallery/{galleries_code}/photo/{photo_id}', [AccountUserController::class, 'deletePhotoFromGallery']);

    // notifications
    Route::get('/notifications', [HomePageController::class, 'getUserNotifications']);
    Route::post('/notifications/mark-as-read', [HomePageController::class, 'markNotificationAsRead']);

    // add photo
    Route::post('/add-photos', [HomePageController::class, 'addPhotos']);

    // post comment
    Route::post('/comments', [PhotoDetailController::class, 'postComment']);

    // Auth user
    Route::get('/user', [AuthUserController::class, 'getUser']);
});
