<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CartController;
use App\Http\Controllers\ItemController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/*Route::resources([
    '/carts' => CartController::class,
]);*/

Route::get('/carts',[CartController::class,'index']);
Route::get('/carts/{cart_id}',[CartController::class,'show']);
Route::get('/carts/{cart_id}/items',[CartController::class,'show_items']);

Route::post('/carts',[CartController::class,'store']);

Route::put('/carts/{cart_id}/items',[CartController::class,'add_items']);

Route::delete('/carts/{cart_id}/items/{pivot_id}',[CartController::class,'delete_item']);