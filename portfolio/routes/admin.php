<?php

use Illuminate\Support\Facades\Route;

//dashboard
Route::get('/dashboard',[\App\Http\Controllers\Admin\dashboardController::class,'index']);

//photo
Route::get('/photo',[\App\Http\Controllers\Admin\photoController::class,'index']);
Route::get('/photoPending',[\App\Http\Controllers\Admin\photoController::class,'photoPending']);
Route::get('/photoRejected',[\App\Http\Controllers\Admin\photoController::class,'photoRejected']);

// add photo
Route::get('/photo/create', [\App\Http\Controllers\Admin\photoController::class, 'create']);
Route::post('/photo/store', [\App\Http\Controllers\Admin\photoController::class, 'store']);

// Edit photo
Route::get('/photo/{id}/edit', [\App\Http\Controllers\Admin\photoController::class, 'edit']);
Route::put('/photo/update/{id}', [\App\Http\Controllers\Admin\photoController::class, 'update']);

// Delete photo
Route::delete('/photo/delete/{id}', [\App\Http\Controllers\Admin\photoController::class, 'destroy']);


