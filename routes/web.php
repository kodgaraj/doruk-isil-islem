<?php

use App\Http\Controllers\BildirimlerController;
use App\Http\Controllers\FirinlarController;
use App\Http\Controllers\FirmaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IsilIslemController;
use App\Http\Controllers\IslemDurumlariController;
use App\Http\Controllers\IslemTurleriController;
use App\Http\Controllers\KullanicilarController;
use App\Http\Controllers\LogKayitlariController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\SablonController;
use App\Http\Controllers\MalzemeController;
use App\Http\Controllers\PDFExportController;
use App\Http\Controllers\RaporlamaController;
use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiparisController;
use App\Http\Controllers\TeklifController;
use App\Http\Controllers\TumIslemlerController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\SistemController;


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

Route::get('/update/{sifre}', [UpdateController::class, 'index']);
Route::get("/pdf-exports/{tur}", [PDFExportController::class, "index"])->name("pdfExports");
Route::get("/pdf-exports2/{tur}/{id}", [PDFExportController::class, "index2"])->name("pdfExports2");

Route::group(['middleware' => ['auth','kontrol']], function () {
    // sayfalar
    Route::get('/', [HomeController::class, "index"])->name("home");
    Route::get('/kisitlamalar', [SistemController::class, "index"])->name("kisitlamalar");
    Route::post('/kisitGuncelle', [SistemController::class, "kisitGuncelle"])->name("kisitGuncelle");
    Route::get('/siparis-formu', [SiparisController::class, 'index'])->name("siparis-formu")->middleware(['can:siparis_listeleme']);
    Route::get('/isil-islemler', [IsilIslemController::class, 'index'])->name("isil-islemler")->middleware(['can:isil_islem_formu_listeleme']);
    Route::get('/tum-islemler', [TumIslemlerController::class, 'index'])->name("tum-islemler")->middleware(['can:isil_islem_listeleme']);
    Route::get('/kullanicilar', [KullanicilarController::class, 'index'])->name("kullanicilar")->middleware(['can:kullanici_listeleme']);
    Route::get('/roller', [RolController::class, 'index'])->name("roller")->middleware(['can:rol_listeleme']);
    Route::get('/raporlama', [RaporlamaController::class, 'index'])->name("raporlama")->middleware(['can:rapor_listeleme']);
    Route::get('/log-kayitlari', [LogKayitlariController::class, 'index'])->name("log-kayitlari")->middleware(['can:log_listeleme']);
    Route::get('/login-kayitlari', [LogKayitlariController::class, 'login'])->name("login-kayitlari")->middleware(['can:log_listeleme']);
    Route::get('/firinlar', [FirinlarController::class, 'index'])->name("firinlar")->middleware(['can:firin_listeleme']);
    Route::get('/firmalar', [FirmaController::class, 'index'])->name("firmalar")->middleware(['can:firma_listeleme']);
    Route::get('/bildirimler', [BildirimlerController::class, 'index'])->name("bildirimler");
    Route::get('/logsuccessfullGetir', [BildirimlerController::class, 'index'])->name("logsuccessfullGetir");

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
    Route::get('/firinSarjGrupluIslemleriGetir', [IsilIslemController::class, 'firinSarjGrupluIslemleriGetir'])->name("firinSarjGrupluIslemleriGetir");
    Route::post('/sarjIslemleriBaslat', [IsilIslemController::class, 'sarjIslemleriBaslat'])->name("sarjIslemleriBaslat");
    Route::post('/sarjIslemleriTamamla', [IsilIslemController::class, 'sarjIslemleriTamamla'])->name("sarjIslemleriTamamla");
    Route::post('/islemBol', [IsilIslemController::class, 'islemBol'])->name("islemBol");

    Route::get('/islemTurleriGetir', [IslemTurleriController::class, 'islemTurleriGetir'])->name("islemTurleriGetir");
    Route::post('/islemTuruEkle', [IslemTurleriController::class, 'islemTuruEkle'])->name("islemTuruEkle");

    Route::get('/islemDurumlariGetir', [IslemDurumlariController::class, 'islemDurumlariGetir'])->name("islemDurumlariGetir");

    Route::get('/malzemeleriGetir', [MalzemeController::class, 'malzemeleriGetir'])->name("malzemeleriGetir");
    Route::post('/malzemeEkle', [MalzemeController::class, 'malzemeEkle'])->name("malzemeEkle");

    Route::get('/firmalariGetir', [FirmaController::class, 'firmalariGetir'])->name("firmalariGetir");
    Route::post('/firmaSil', [FirmaController::class, 'firmaSil'])->name("firmaSil");
    Route::post('/firmaEkle', [FirmaController::class, 'firmaEkle'])->name("firmaEkle");
    Route::post('/firmalariBirlestir', [FirmaController::class, 'firmalariBirlestir'])->name("firmalariBirlestir");

    Route::get('/firinlariGetir', [FirinlarController::class, 'firinlariGetir'])->name("firinlariGetir");
    Route::post('/firinKaydet', [FirinlarController::class, 'firinKaydet'])->name("firinKaydet");
    Route::post('/firinSil', [FirinlarController::class, 'firinSil'])->name("firinSil");

    Route::get('/kullanicilariGetir', [KullanicilarController::class, 'kullanicilariGetir'])->name("kullanicilariGetir");
    Route::post('/kullaniciKaydet', [KullanicilarController::class, 'kullaniciKaydet'])->name("kullaniciKaydet");
    Route::post('/kullaniciSil', [KullanicilarController::class, 'kullaniciSil'])->name("kullaniciSil");

    Route::post('/rolKaydet', [RolController::class, 'rolKaydet'])->name("rolKaydet");
    Route::get('/rolleriGetir', [RolController::class, 'rolleriGetir'])->name("rolleriGetir");
    Route::post('/rolSil', [RolController::class, 'rolSil'])->name("rolSil");

    Route::get('/yillikCiroGetir', [RaporlamaController::class, 'yillikCiroGetir'])->name("yillikCiroGetir");
    Route::get('/aylikCiroGetir', [RaporlamaController::class, 'aylikCiroGetir'])->name("aylikCiroGetir");
    Route::get('/firinBazliTonaj', [RaporlamaController::class, 'firinBazliTonaj'])->name("firinBazliTonaj");
    Route::get('/firmaBazliBilgileriGetir', [RaporlamaController::class, 'firmaBazliBilgileriGetir'])->name("firmaBazliBilgileriGetir");
    Route::get('/firinBazliIslemTurleriGetir', [RaporlamaController::class, 'firinBazliIslemTurleriGetir'])->name("firinBazliIslemTurleriGetir");

    Route::get('/logKayitlariGetir', [LogKayitlariController::class, 'logKayitlariGetir'])->name("logKayitlariGetir");
    Route::get('/loginKayitlariGetir', [LogKayitlariController::class, 'loginKayitlariGetir'])->name("loginKayitlariGetir");

    Route::get('/bildirimleriGetir', [BildirimlerController::class, 'bildirimleriGetir'])->name("bildirimleriGetir");
    Route::get('/okunmamisBildirimSayisiGetir', [BildirimlerController::class, 'okunmamisBildirimSayisiGetir'])->name("okunmamisBildirimSayisiGetir");
    Route::get('/miniBildirimleriGetir', [BildirimlerController::class, 'miniBildirimleriGetir'])->name("miniBildirimleriGetir");

    Route::post("/createPDF", [PDFExportController::class, "createPDF"]);
    Route::post("/createPDF2", [PDFExportController::class, "createPDF2"]);

    Route::get('/sablonlar', [SablonController::class, 'index'])->name("sablonlar");
    Route::get('/sablonlariGetir', [SablonController::class, 'sablonlariGetir'])->name("sablonlariGetir");
    Route::post('/sablonEkle', [SablonController::class, 'sablonEkle'])->name("sablonEkle");
    Route::post('/sablonSil', [SablonController::class, 'sablonSil'])->name("sablonSil");

    Route::get('/teklifler/{firmaId?}', [TeklifController::class, 'index'])->name("teklifler");
    Route::get('/teklifleriGetir', [TeklifController::class, 'teklifleriGetir'])->name("teklifleriGetir");
    Route::post('/teklifEkle', [TeklifController::class, 'teklifEkle'])->name("teklifEkle");
    Route::post('/teklifSil', [TeklifController::class, 'teklifSil'])->name("teklifSil");


    Route::post('/mailGonder', [MailController::class, "mail"]);
});


require __DIR__.'/auth.php';
