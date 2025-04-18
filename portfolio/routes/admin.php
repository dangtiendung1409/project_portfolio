<?php

use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'role:admin'])->group(function () {
//dashboard
Route::get('/dashboard',[\App\Http\Controllers\Admin\dashboardController::class,'dashboard'])
    ->name('admin.dashboard');

//photo management
Route::get('/photo',[\App\Http\Controllers\Admin\photoController::class,'index']);
Route::get('/photo/details/{id}', [\App\Http\Controllers\Admin\photoController::class, 'showDetails'])
    ->name('photo.details');
Route::get('/photo/comment/{photo_id}', [\App\Http\Controllers\Admin\PhotoController::class, 'showComment'])
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
Route::get('/reports/photo', [\App\Http\Controllers\Admin\reportController::class, 'getPhotoReports']);
Route::get('/reports/comment', [\App\Http\Controllers\Admin\reportController::class, 'getCommentReports']);
Route::get('/reports/gallery', [\App\Http\Controllers\Admin\reportController::class, 'getGalleryReports']);

Route::post('/report/updateStatus/{id}/{action}', [\App\Http\Controllers\Admin\reportController::class, 'updateStatus'])
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

//Account setting
Route::get('/profile', [\App\Http\Controllers\Admin\accountSettingController::class, 'profile'])
    ->name('admin.profile');
Route::post('/profile', [\App\Http\Controllers\Admin\accountSettingController::class, 'updateProfile'])
    ->name('admin.updateProfile');

Route::get('/change-password', [\App\Http\Controllers\Admin\accountSettingController::class, 'changePassword'])
    ->name('admin.changePassword');
Route::post('/update-password', [\App\Http\Controllers\Admin\accountSettingController::class, 'updatePassword'])
    ->name('admin.updatePassword');

//category management
Route::get('/contact', [\App\Http\Controllers\Admin\contactController::class, 'index'])->name('admin.contact.index');

Route::get('/admin/contact/send/{id}', [\App\Http\Controllers\Admin\contactController::class, 'showSendForm'])
    ->name('admin.contact.send');
Route::post('/admin/contact/send/{id}', [\App\Http\Controllers\Admin\contactController::class, 'sendContact']);


//blog management
Route::get('/blogs', [\App\Http\Controllers\Admin\blogController::class, 'index'])
    ->name('admin.blogs.index');
Route::get('/blogs/create', [\App\Http\Controllers\Admin\blogController::class, 'create'])
    ->name('admin.blogs.create');
Route::post('/blogs/store', [\App\Http\Controllers\Admin\blogController::class, 'store'])
    ->name('admin.blogs.store');
Route::get('/blogs/edit/{id}', [\App\Http\Controllers\Admin\blogController::class, 'edit'])
    ->name('admin.blogs.edit');
Route::post('/blogs/update/{id}', [\App\Http\Controllers\Admin\blogController::class, 'update'])
    ->name('admin.blogs.update');
Route::post('/blogs/delete/{id}', [\App\Http\Controllers\Admin\blogController::class, 'destroy'])
    ->name('admin.blogs.destroy');
});
