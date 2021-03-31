<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PenjualanController;
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

Route::get('/category', [KategoriController::class, 'index']);
Route::post('/category', [KategoriController::class, 'store']);
Route::put('/category/{id}', [KategoriController::class, 'update']);
Route::delete('/category/{id}', [KategoriController::class, 'destroy']);

Route::get('customer/{email}', [PelangganController::class, 'profile']);
Route::patch('customer/username/{id}', [PelangganController::class, 'update_username']);
Route::patch('customer/email/{id}', [PelangganController::class, 'update_email']);
Route::post('customer/photo/{id}', [PelangganController::class, 'photo']);

Route::post('/signup', [PelangganController::class, 'signup']);
Route::post('/signin', [PelangganController::class, 'signin']);
Route::delete('/signout', [PelangganController::class, 'signout']);

Route::get('/item', [BarangController::class, 'index']);
Route::post('/item', [BarangController::class, 'store']);
Route::patch('/item/{id}', [BarangController::class, 'update']);
Route::delete('/item/{id}', [BarangController::class, 'destroy']);

Route::get('/transaction', [PenjualanController::class, 'index']);
Route::get('/transaction/user/{id}', [PenjualanController::class, 'user']);
Route::get('/transaction/detail/{id}', [PenjualanController::class, 'detail']);
Route::post('/transaction', [PenjualanController::class, 'store']);
Route::delete('/transaction/{id}', [PenjualanController::class, 'destroy']);
