<?php

use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'role:admin'])->group(function () {
//dashboard
Route::get('/dashboard',[\App\Http\Controllers\Admin\dashboardController::class,'index'])
    ->name('admin.dashboard');

//photo management
Route::get('/photo',[\App\Http\Controllers\Admin\photoController::class,'index']);
//photo pending
Route::get('/photoPending',[\App\Http\Controllers\Admin\photoController::class,'photoPending'])
    ->name('admin.photoPending');
Route::get('/photoPending/detail/{id}', [\App\Http\Controllers\Admin\photoController::class, 'detailPhotoPending']);
Route::post('/admin/updatePhotoStatus/{id}', [\App\Http\Controllers\Admin\PhotoController::class, 'updatePhotoStatus'])
    ->name('admin.updatePhotoStatus');
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
// comment photo
Route::get('/photo/comments/{id}', [\App\Http\Controllers\Admin\photoController::class, 'showComments']);
Route::post('/photo/comment/{id}/status/{status}', [\App\Http\Controllers\Admin\photoController::class, 'updateStatus'])
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

});
