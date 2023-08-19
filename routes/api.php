<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuth;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/users/register', [UserController::class, 'register'])->name('user.register');
Route::post('/users/login', [UserController::class, 'login'])->name('user.login');

Route::middleware([ApiAuth::class])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/users/current', 'show');
        Route::put('/users/current', 'update');
        Route::delete('/users/logout', 'destroy');
    });

    Route::controller(ContactController::class)->group(function () {
        Route::post('/contacts', 'store');
        Route::get('/contacts/{id}', 'show');
        Route::put('/contacts/{id}', 'update');
        Route::delete('/contacts/{id}', 'destroy');
    });
});
