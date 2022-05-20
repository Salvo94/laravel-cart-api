<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CartController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




Route::post('/login', [LoginController::class,'authenticate'])->name("login");
Route::post('/register', [LoginController::class,'create_user'])->name("register");
Route::get('/user', [LoginController::class,'get_user'])->name("user")->middleware('auth:sanctum');
Route::get('/logout', [LoginController::class,'logout'])->name("logout")->middleware('auth:sanctum');


/*Route::resources([
    '/carts' => CartController::class,
]);*/

Route::middleware('auth:sanctum')->group(function () {
    //Gestione carrello (carts)
    Route::get('/carts', [CartController::class,'index']);
    Route::get('/carts/{cart_id}', [CartController::class,'show']);
    Route::get('/carts/{cart_id}/items', [CartController::class,'show_items']);

    Route::post('/carts', [CartController::class,'store']);

    Route::put('/carts/{cart_id}/items', [CartController::class,'add_items']);

    Route::delete('/carts/{cart_id}/items/{pivot_id}', [CartController::class,'remove_cart_item']);


    //Gestione articoli (items)
    Route::get('/items', [ItemController::class,'index']);

    Route::post('/items', [ItemController::class,'store']);

    Route::delete('/items/{item_id}', [ItemController::class,'delete_item']);
});

Route::fallback(function () {
    abort(404, 'API resource not found');
});
