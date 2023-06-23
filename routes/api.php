<?php

use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RestaurantController;
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
     * ROUTES FOR RESTAURANTS
     */
    Route::get('/restaurants', [RestaurantController::class, 'index']);
    Route::get('/count-restaurants', [RestaurantController::class, 'countRestaurants']);
    Route::get('/restaurant/{id}', [RestaurantController::class, 'show']);


    /**
     * ROUTES FOR MENUS
     */
    Route::get('/menus', [MenuItemController::class, 'index']);
    Route::get('/count-menu', [MenuItemController::class, 'countMenus']);
    Route::get('/menu/{id}', [MenuItemController::class, 'show']);



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


    /**
     * ROUTES FOR RESTAURANTS
     */
    Route::post('/restaurant', [RestaurantController::class, 'store']);
    Route::post('/restaurant/{id}', [RestaurantController::class, 'update']);
    Route::delete('/del-restaurant/{id}', [RestaurantController::class, 'destroy']);


    /**
    * ROUTES FOR MENU ITEMS
    */
    Route::post('/menu', [MenuItemController::class, 'store']);
    Route::post('/menu/{id}', [MenuItemController::class, 'update']);
    Route::delete('/del-menu/{id}', [MenuItemController::class, 'destroy']);



    /**
    * ROUTES FOR ORDERS
    */
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/count-orders', [OrderController::class, 'countOrders']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::post('/order/{menu_id}', [OrderController::class, 'store']);
    Route::put('/order/{id}', [OrderController::class, 'update']);
    Route::put('/process-order/{id}', [OrderController::class, 'processingOrder']);
    Route::put('/delivering-order/{id}', [OrderController::class, 'deliveringOrder']);
    Route::put('/delivered-order/{id}', [OrderController::class, 'deliveredOrder']);
    Route::put('/received-order/{id}', [OrderController::class, 'receivedOrder']);
    Route::put('/cancel-order/{id}', [OrderController::class, 'cancelOrder']);
    Route::delete('/del-order/{id}', [OrderController::class, 'destroy']);















});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
