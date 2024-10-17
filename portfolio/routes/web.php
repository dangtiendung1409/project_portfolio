<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});
Route::get('login', [\App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::post('logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

Route::get('/apiDocs', function () {
    return view('apiDocs');
});

//Route::middleware(['auth', 'role:user'])->group(function () {
//    Route::get('/user/dashboard', function () {
//        return view('user.dashboard');
//    })->name('user.dashboard');
//});
