<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
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

/**
 * AUTHENTICATION ROUTES
 */
Route::post('/sign-in', [UserController::class, 'login']);
Route::post('/sign-up', [UserController::class, 'register']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('password.email'); 
Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.update');

/**
 * ROUTES THAT REQUIRE USERS TO BE AUTHENTICATED BEFORE HAVING ACCESS
 */
Route::group(['middleware' => 'auth:sanctum'], function(){
    /**
    * ROUTES FOR USER ACCOUNTS
    */
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/change-password', [UserController::class, 'changePassword']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::get('/users', [UserController::class, 'index']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
