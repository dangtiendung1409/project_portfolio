<?php

use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'role:admin'])->group(function () {
//dashboard
Route::get('/dashboard',[\App\Http\Controllers\Admin\dashboardController::class,'dashboard'])
    ->name('admin.dashboard');

//photo management
Route::get('/photo',[\App\Http\Controllers\Admin\photoController::class,'index']);

// list comment photo
Route::get('/photo/comment/{photo_image_id}', [\App\Http\Controllers\Admin\PhotoController::class, 'showComment'])
    ->name('photo.showComment');

//photo pending
Route::get('/photoPending',[\App\Http\Controllers\Admin\photoController::class,'photoPending'])
    ->name('admin.photoPending');
Route::post('/photo/updateStatus/{id}/{status}', [\App\Http\Controllers\Admin\photoController::class, 'updateStatus'])
    ->name('admin.photoPending.updateStatus');

//photo rejected
Route::get('/photoRejected',[\App\Http\Controllers\Admin\photoController::class,'photoRejected']);
// add photo
Route::get('/photo/create', [\App\Http\Controllers\Admin\photoController::class, 'create']);
Route::post('/photo/store', [\App\Http\Controllers\Admin\photoController::class, 'store']);
// Edit photo
Route::get('/photo/edit/{id}', [\App\Http\Controllers\Admin\photoController::class, 'edit']);
Route::post('/photo/update/{id}', [\App\Http\Controllers\Admin\photoController::class, 'update']);
// Delete photo
Route::post('/photo/delete/{id}', [\App\Http\Controllers\Admin\photoController::class, 'destroy']);

// comment photo management
Route::get('/listComment', [\App\Http\Controllers\Admin\commentController::class, 'listComment'])
    ->name('admin.comment.listComment');
Route::post('/comment/updateStatus/{id}/{status}', [\App\Http\Controllers\Admin\commentController::class, 'updateStatus'])
    ->name('admin.comment.updateStatus');


//category management
Route::get('/category',[\App\Http\Controllers\Admin\categoryController::class,'index']);
// add category
Route::get('/category/create',[\App\Http\Controllers\Admin\categoryController::class,'create']);
Route::post('/category/store',[\App\Http\Controllers\Admin\categoryController::class,'store']);
// Edit category
Route::get('/category/edit/{id}', [\App\Http\Controllers\Admin\categoryController::class, 'edit'])->name('category.edit');
Route::post('/category/update/{id}', [\App\Http\Controllers\Admin\categoryController::class, 'update'])->name('category.update');
// Delete category
Route::post('/category/delete/{id}', [\App\Http\Controllers\Admin\categoryController::class, 'destroy'])->name('category.destroy');

//report management
Route::get('/reportPending',[\App\Http\Controllers\Admin\reportController::class,'reportPending']);
Route::get('/reportResolved',[\App\Http\Controllers\Admin\reportController::class,'reportResolved']);
Route::post('/report/updateStatus/{id}/{action}', [\App\Http\Controllers\Admin\ReportController::class, 'updateStatus'])
     ->name('admin.report.updateStatus');

// user management
Route::get('/users', [\App\Http\Controllers\Admin\userController::class, 'index'])
    ->name('admin.users.index');
Route::get('/usersInActive', [\App\Http\Controllers\Admin\userController::class, 'usersInActive'])
    ->name('admin.users.usersInActive');
// unlock account user
Route::post('/unlock-user/{id}', [\App\Http\Controllers\Admin\userController::class, 'unlockUser'])
    ->name('admin.unlockUser');
// list photo user
Route::get('/users/photos/{id}', [\App\Http\Controllers\Admin\userController::class, 'getUserPhotos'])
    ->name('user.photos');
// galleries photo user
Route::get('/users/galleries/{id}', [\App\Http\Controllers\Admin\userController::class, 'getUserGalleries'])
    ->name('user.galleries');
Route::get('/users/galleries/photos/{id}', [\App\Http\Controllers\Admin\UserController::class, 'getGalleryPhotos'])
    ->name('galleries.photos');

});
