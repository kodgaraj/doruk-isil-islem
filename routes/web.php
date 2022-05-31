<?php

use App\Http\Controllers\isilIslemController;
use App\Http\Controllers\IslemTurleriController;
use App\Http\Controllers\MalzemeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiparisController;

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

    Route::get('/siparis-formu', [SiparisController::class, 'siparisEklemeFormu'])->name("siparis-formu");
    Route::get('/siparisler', [SiparisController::class, 'siparisler'])->name("siparisler");
    Route::get('/numaralariGetir', [SiparisController::class, 'numaralariGetir'])->name("numaralariGetir");
    Route::get('/siparisDurumlariGetir', [SiparisController::class, 'siparisDurumlariGetir'])->name("siparisDurumlariGetir");
    Route::get('/firmalariGetir', [SiparisController::class, 'firmalariGetir'])->name("firmalariGetir");
    Route::post('/siparisKaydet', [SiparisController::class, 'siparisKaydet'])->name("siparisKaydet");

    Route::get('/islemTurleriGetir', [IslemTurleriController::class, 'islemTurleriGetir'])->name("islemTurleriGetir");

    Route::get('/malzemeleriGetir', [MalzemeController::class, 'malzemeleriGetir'])->name("malzemeleriGetir");

    Route::get('/siparisDetay', [SiparisController::class, 'siparisDetay'])->name("siparisDetay");
    Route::get('/isil-islem-takip-formu', [isilIslemController::class, 'isilIslemTakipFormu'])->name("isil-islem-formu");
});



require __DIR__.'/auth.php';
