<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PelangganController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/kategori', [KategoriController::class, 'index']);
Route::post('/kategori', [KategoriController::class, 'store']);
Route::put('/kategori/{id}', [KategoriController::class, 'update']);
Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

Route::post('/signup', [PelangganController::class, 'signup']);
Route::post('/signin', [PelangganController::class, 'signin']);
Route::delete('/signout', [PelangganController::class, 'signout']);

Route::get('/item', [BarangController::class, 'index']);
Route::post('/item', [BarangController::class, 'store']);
Route::patch('/item/{id}', [BarangController::class, 'update']);
Route::delete('/item/{id}', [BarangController::class, 'destroy']);
