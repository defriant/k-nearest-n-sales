<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('login');
    })->name('index');
    Route::post('/login-attempt', [WebController::class, 'login_attempt']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [WebController::class, 'logout']);

    Route::get('/dashboard', [WebController::class, 'dashboard']);
    Route::get('/data-pendapatan', [WebController::class, 'data_pendapatan']);

    Route::get('/master-produk', function () {
        return view('master-produk');
    });
    Route::get('/master-produk/get', [WebController::class, 'get_produk']);
    Route::post('/master-produk/input', [WebController::class, 'input_produk']);
    Route::post('/master-produk/update', [WebController::class, 'update_produk']);
    Route::post('/master-produk/delete', [WebController::class, 'delete_produk']);

    Route::get('/transaksi', [WebController::class, 'transaksi']);
    Route::post('/transaksi/get', [WebController::class, 'get_transaksi']);
    Route::post('/transaksi/input', [WebController::class, 'input_transaksi']);

    Route::get('/transaksi-perbulan', function () {
        return view('transaksi-perbulan');
    });
    Route::post('/transaksi-perbulan/get', [WebController::class, 'get_transaksi_perbulan']);

    Route::get('/kelola-data-penjualan', [WebController::class, 'kelola_data_penjualan']);
    Route::get('/data-penjualan', [WebController::class, 'data_penjualan']);

    Route::post('/input-data-penjualan', [WebController::class, 'input']);
    Route::post('/edit-data-penjualan', [WebController::class, 'edit']);

    Route::get('/prediksi-penjualan', [WebController::class, 'prediksi']);
    Route::post('/knn', [WebController::class, 'knn']);
});
