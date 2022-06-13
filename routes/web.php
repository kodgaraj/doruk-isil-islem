<?php

use App\Http\Controllers\FirinlarController;
use App\Http\Controllers\FirmaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IsilIslemController;
use App\Http\Controllers\IslemDurumlariController;
use App\Http\Controllers\IslemTurleriController;
use App\Http\Controllers\MalzemeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiparisController;
use App\Http\Controllers\TumIslemlerController;

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
    // sayfalar
    Route::get('/', [HomeController::class, "index"])->name("home");
    Route::get('/siparis-formu', [SiparisController::class, 'index'])->name("siparis-formu");
    Route::get('/isil-islemler', [IsilIslemController::class, 'index'])->name("isil-islemler");
    Route::get('/tum-islemler', [TumIslemlerController::class, 'index'])->name("tum-islemler");

    // apiler
    Route::get('/siparisler', [SiparisController::class, 'siparisler'])->name("siparisler");
    Route::get('/numaralariGetir', [SiparisController::class, 'numaralariGetir'])->name("numaralariGetir");
    Route::get('/siparisDurumlariGetir', [SiparisController::class, 'siparisDurumlariGetir'])->name("siparisDurumlariGetir");
    Route::post('/siparisKaydet', [SiparisController::class, 'siparisKaydet'])->name("siparisKaydet");
    Route::post('/siparisDetay', [SiparisController::class, 'siparisDetay'])->name("siparisDetay");
    Route::post('/siparisSil', [SiparisController::class, 'siparisSil'])->name("siparisSil");

    Route::get('/formlar', [IsilIslemController::class, 'formlar'])->name("formlar");
    Route::get('/takipNumarasiGetir', [IsilIslemController::class, 'takipNumarasiGetir'])->name("takipNumarasiGetir");
    Route::get('/firmaGrupluIslemleriGetir', [IsilIslemController::class, 'firmaGrupluIslemleriGetir'])->name("firmaGrupluIslemleriGetir");
    Route::post('/formKaydet', [IsilIslemController::class, 'formKaydet'])->name("formKaydet");
    Route::post('/formDetay', [IsilIslemController::class, 'formDetay'])->name("formDetay");
    Route::post('/formSil', [IsilIslemController::class, 'formSil'])->name("formSil");
    Route::get('/islemler', [IsilIslemController::class, 'islemler'])->name("islemler");
    Route::post('/islemDurumuDegistir', [IsilIslemController::class, 'islemDurumuDegistir'])->name("islemDurumuDegistir");
    Route::post('/islemTekrarEt', [IsilIslemController::class, 'islemTekrarEt'])->name("islemTekrarEt");
    Route::post('/islemTamamlandiGeriAl', [IsilIslemController::class, 'islemTamamlandiGeriAl'])->name("islemTamamlandiGeriAl");

    Route::get('/islemTurleriGetir', [IslemTurleriController::class, 'islemTurleriGetir'])->name("islemTurleriGetir");
    Route::post('/islemTuruEkle', [IslemTurleriController::class, 'islemTuruEkle'])->name("islemTuruEkle");

    Route::get('/islemDurumlariGetir', [IslemDurumlariController::class, 'islemDurumlariGetir'])->name("islemDurumlariGetir");

    Route::get('/malzemeleriGetir', [MalzemeController::class, 'malzemeleriGetir'])->name("malzemeleriGetir");
    Route::post('/malzemeEkle', [MalzemeController::class, 'malzemeEkle'])->name("malzemeEkle");

    Route::get('/firinlariGetir', [FirinlarController::class, 'firinlariGetir'])->name("firinlariGetir");

    Route::post('/firmaEkle', [FirmaController::class, 'firmaEkle'])->name("firmaEkle");
    Route::get('/firmalariGetir', [FirmaController::class, 'firmalariGetir'])->name("firmalariGetir");
});



require __DIR__.'/auth.php';
