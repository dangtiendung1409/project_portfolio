<?php

use Illuminate\Support\Facades\Route;

//dashboard
Route::get('/dashboard',[\App\Http\Controllers\Admin\dashboardController::class,'index']);

//photo
Route::get('/photo',[\App\Http\Controllers\Admin\photoController::class,'index']);
Route::get('/photoPending',[\App\Http\Controllers\Admin\photoController::class,'photoPending']);
Route::get('/photoRejected',[\App\Http\Controllers\Admin\photoController::class,'photoRejected']);
