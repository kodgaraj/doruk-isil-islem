<?php

use App\Http\Controllers\isilIslemController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\siparisTakipController;

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

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        return view("index");
    })->name("home");
    Route::get('/siparis-formu', [siparisTakipController::class, 'siparisEklemeFormu'])->name("siparis-formu");
    Route::get('/isil-islem-takip-formu', [isilIslemController::class, 'isilIslemTakipFormu'])->name("isil-islem-formu");
});



require __DIR__.'/auth.php';
