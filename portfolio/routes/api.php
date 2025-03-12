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
use App\Http\Controllers\Front\FollowController;
use App\Http\Controllers\Front\BlockController;
use App\Http\Controllers\Front\ReportController;
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
Route::get('/categories', [HomePageController::class, 'getAllCategories']);
Route::get('/tags', [HomePageController::class, 'getAllTags']);

// for you
Route::get('/getPhotoData', [HomePageController::class, 'getImages']);
// follow
Route::get('/getFollowData', [HomePageController::class, 'getFollows']);

// explore
Route::get('/top-liked-photos', [HomePageController::class, 'topLikedPhotos']);
Route::get('/top-users-with-photos', [HomePageController::class, 'getTopUsersWithPhotos']);
Route::get('/top-categories', [HomePageController::class, 'getTopCategories']);
Route::get('/top-liked-galleries', [HomePageController::class, 'getTopLikedGalleries']);


// photo details
Route::get('/getPhotoDetail/{token}', [PhotoDetailController::class, 'getPhotoDetail']);
Route::get('/comments/{token}', [PhotoDetailController::class, 'getCommentsByPhotoToken']);
Route::get('/photo-likes/{token}', [PhotoDetailController::class, 'getPhotoLikes']);
Route::get('/related-photos/{token}', [PhotoDetailController::class, 'getRelatedPhotos']);
Route::get('/related-galleries/{token}', [PhotoDetailController::class, 'getRelatedGalleries']);


//Profile user
Route::get('/user-by-username/{username}', [ProfileController::class, 'getUserByUserName']);
Route::get('/photos-by-username/{username}', [ProfileController::class, 'getPhotosByUserName']);
Route::get('/galleries-by-username/{username}', [ProfileController::class, 'getGalleriesByUserName']);
Route::get('/gallery-details-user/{galleries_code}', [ProfileController::class, 'getGalleryDetailUser']);
Route::get('/total-likes/{username}', [ProfileController::class, 'getTotalLikesByUsername']);
Route::get('/getFollowingUser/{username}', [FollowController::class, 'followingUser']); // Danh sách người dùng mà user được chỉ định đang theo dõi
Route::get('/getFollowersUser/{username}', [FollowController::class, 'followersUser']); // Danh sách những người theo dõi user được chỉ định

// contact
Route::post('/contact', [HomePageController::class, 'sendContact']);

//Category
Route::get('/categories/photos', [CategoryController::class, 'getPhotosByCategorySlugs']);

// Search photos
Route::get('/search-photos', [SearchController::class, 'searchPhotos']);

Route::middleware('auth:api')->group(function () {
    // home page
    Route::get('/recent-followed-photos', [HomePageController::class, 'getRecentFollowedPhotos']);
    Route::get('/recent-followed-galleries', [HomePageController::class, 'getRecentFollowedGalleries']);

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
    Route::delete('/like/{like_id}', [AccountUserController::class, 'deleteLike']);
    Route::post('/like-photo', [HomePageController::class, 'likePhoto']);
    Route::post('/unlike-photo', [HomePageController::class, 'unlikePhoto']);
    Route::post('/like-gallery', [HomePageController::class, 'likeGallery']);
    Route::post('/unlike-gallery', [HomePageController::class, 'unlikeGallery']);


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

    //comment
    Route::post('/comments', [PhotoDetailController::class, 'postComment']);
    Route::delete('/comments/{id}', [PhotoDetailController::class, 'deleteComment']);

    // follow
    Route::post('/follow', [FollowController::class, 'follow']);
    Route::post('/unfollow/{following_id}', [FollowController::class, 'unfollow']);
    Route::get('/following-list', [FollowController::class, 'followingList']); //Danh sách người dùng mà user hiện tại đang theo dõi
    Route::get('/followers-list', [FollowController::class, 'followersList']); //Danh sách những người theo dõi user hiện tại

    // block
    Route::post('/block', [BlockController::class, 'blockUser']);
    Route::post('/unblock', [BlockController::class, 'unblockUser']);
    Route::get('/blocked-users', [BlockController::class, 'getBlockedUsers']); // Lấy danh sách người bị chặn bởi user login

    // report
    Route::post('/report', [ReportController::class, 'store']);

    // Auth user
    Route::get('/user', [AuthUserController::class, 'getUser']);
});
