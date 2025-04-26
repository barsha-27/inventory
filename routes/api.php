<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Inventory\CategoryController;
use App\Http\Controllers\API\Inventory\ProductController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post("register",[AuthController::class,"register"]);
Route::post("login",[AuthController::class,"login"]);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource("category", CategoryController::class);
    Route::resource("product", ProductController::class);
    Route::get('product/category/{categoryId}', [ProductController::class, 'byCategory']);
    Route::get('/product/export/csv', [ProductController::class, 'exportCsv']);
    Route::get('/product/export/pdf', [ProductController::class, 'exportPdf']);

});



