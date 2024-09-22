<?php

use Illuminate\Support\Facades\Route;

//dashboard
Route::get('/dashboard',[\App\Http\Controllers\Admin\dashboardController::class,'index']);

//photo management
Route::get('/photo',[\App\Http\Controllers\Admin\photoController::class,'index']);

//photo pending
Route::get('/photoPending',[\App\Http\Controllers\Admin\photoController::class,'photoPending'])
    ->name('admin.photoPending');
Route::get('/photoPending/detail/{id}', [\App\Http\Controllers\Admin\photoController::class, 'detailPhotoPending']);
Route::put('/admin/updatePhotoStatus/{id}', [\App\Http\Controllers\Admin\PhotoController::class, 'updatePhotoStatus'])
    ->name('admin.updatePhotoStatus');

//photo rejected
Route::get('/photoRejected',[\App\Http\Controllers\Admin\photoController::class,'photoRejected']);

// add photo
Route::get('/photo/create', [\App\Http\Controllers\Admin\photoController::class, 'create']);
Route::post('/photo/store', [\App\Http\Controllers\Admin\photoController::class, 'store']);

// Edit photo
Route::get('/photo/edit/{id}', [\App\Http\Controllers\Admin\photoController::class, 'edit']);
Route::put('/photo/update/{id}', [\App\Http\Controllers\Admin\photoController::class, 'update']);

// Delete photo
Route::delete('/photo/delete/{id}', [\App\Http\Controllers\Admin\photoController::class, 'destroy']);


