<?php

use App\Http\Controllers\FirinlarController;
use App\Http\Controllers\IsilIslemController;
use App\Http\Controllers\IslemDurumlariController;
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

    Route::get('/siparis-formu', [SiparisController::class, 'index'])->name("siparis-formu");
    Route::get('/isil-islemler', [IsilIslemController::class, 'index'])->name("isil-islemler");
    
    Route::get('/siparisler', [SiparisController::class, 'siparisler'])->name("siparisler");
    Route::get('/numaralariGetir', [SiparisController::class, 'numaralariGetir'])->name("numaralariGetir");
    Route::get('/siparisDurumlariGetir', [SiparisController::class, 'siparisDurumlariGetir'])->name("siparisDurumlariGetir");
    Route::get('/firmalariGetir', [SiparisController::class, 'firmalariGetir'])->name("firmalariGetir");
    Route::post('/siparisKaydet', [SiparisController::class, 'siparisKaydet'])->name("siparisKaydet");
    Route::post('/siparisDetay', [SiparisController::class, 'siparisDetay'])->name("siparisDetay");
    Route::post('/siparisSil', [SiparisController::class, 'siparisSil'])->name("siparisSil");

    Route::get('/takipNumarasiGetir', [IsilIslemController::class, 'takipNumarasiGetir'])->name("takipNumarasiGetir");
    Route::get('/firmaGrupluIslemleriGetir', [IsilIslemController::class, 'firmaGrupluIslemleriGetir'])->name("firmaGrupluIslemleriGetir");

    Route::get('/islemTurleriGetir', [IslemTurleriController::class, 'islemTurleriGetir'])->name("islemTurleriGetir");

    Route::get('/islemDurumlariGetir', [IslemDurumlariController::class, 'islemDurumlariGetir'])->name("islemDurumlariGetir");

    Route::get('/malzemeleriGetir', [MalzemeController::class, 'malzemeleriGetir'])->name("malzemeleriGetir");

    Route::get('/firinlariGetir', [FirinlarController::class, 'firinlariGetir'])->name("firinlariGetir");
});



require __DIR__.'/auth.php';
