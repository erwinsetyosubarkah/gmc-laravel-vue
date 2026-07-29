<?php

use App\Http\Controllers\Admin\AdminMyproductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('admin')->group(function () {
    Route::get('/myproducts', [AdminMyproductController::class, 'index']);
    Route::post('/myproducts', [AdminMyproductController::class, 'store']);
    Route::post('/myproducts/{myproduct}', [AdminMyproductController::class, 'edit']);
    Route::delete('/myproducts/{myproduct}', [AdminMyproductController::class, 'destroy']);
});
