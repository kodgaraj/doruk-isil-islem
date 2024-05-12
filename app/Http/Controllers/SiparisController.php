<?php

namespace App\Http\Controllers;

use App\Exports\SiparisExcelExporter;
use App\Models\Firmalar;
use App\Models\IslemDurumlari;
use App\Models\Islemler;
use App\Models\IslemTurleri;
use App\Models\Malzemeler;
use App\Models\SiparisDurumlari;
use App\Models\Siparisler;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiparisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('siparis-formu', [
            "paraBirimleri" => array_values($this->paraBirimleri),
        ])->render();
    }

    public function siparislerX(Request $request)
    {
        // Yavaş çalışan mevcut method
        // Başlangıç zamanı
        $t0 = microtime(true);
        try {
            $filtrelemeler = json_decode($request->filtreleme ?? "[]", true);

            $cikti = isset ($request->cikti) && json_decode($request->cikti) === true;

            $sayfalamaSayisi = $cikti ? 9999 : ($request->sayfalamaSayisi ?? 10);
            $firmaTabloAdi = (new Firmalar())->getTable();
            // Tablo adları zamanı
            $t6 = microtime(true);
            $siparisDurumTabloAdi = (new SiparisDurumlari())->getTable();
            // Durum Tablo adları zamanı
            $t7 = microtime(true);
            $siparisTabloAdi = (new Siparisler())->getTable();
            // Sipariş Tablo adları zamanı
            $t8 = microtime(true);
            $islemTabloAdi = (new Islemler())->getTable();
            // İşlem Tablo adları zamanı
            $t9 = microtime(true);
            $kullaniciTabloAdi = (new User())->getTable();
            // Kullanıcı Tablo adları zamanı
            $t10 = microtime(true);

            // ana sorgu öncesi
            $t2 = microtime(true);
            $siparisler = Siparisler::select(DB::raw("
                    $siparisTabloAdi.id as siparisId,
                    $siparisTabloAdi.ad as siparisAdi,
                    $siparisTabloAdi.irsaliyeNo,
                    $siparisTabloAdi.siparisNo,
                    $siparisTabloAdi.tarih,
                    $siparisTabloAdi.tutar,
                    $siparisTabloAdi.firmaId,
                    $siparisTabloAdi.durumId,
                    $siparisTabloAdi.terminSuresi,
                    $siparisTabloAdi.faturaKesildi,
                    $siparisTabloAdi.faturaTarihi,
                    $siparisTabloAdi.aciklama,
                    $siparisTabloAdi.bitisTarihi,
                    $siparisDurumTabloAdi.ad as siparisDurumAdi,
                    $firmaTabloAdi.firmaAdi,
                    $firmaTabloAdi.sorumluKisi,
                    $kullaniciTabloAdi.name as duzenleyen,
                    COUNT(IF($islemTabloAdi.deleted_at IS NULL AND $islemTabloAdi.bolunmusId IS NULL, $islemTabloAdi.id, NULL)) as islemSayisi,
                    SUM($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                    SUM(
                        IF(
                            $islemTabloAdi.paraBirimi = 'TL',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        )
                    ) as tutarTL,
                    SUM(
                        IF(
                            $islemTabloAdi.paraBirimi = 'USD',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        )
                    ) as tutarUSD,
                    SUM(
                        IF(
                            $islemTabloAdi.paraBirimi = 'EURO',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        )
                    ) as tutarEURO
                "))
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($siparisDurumTabloAdi, $siparisDurumTabloAdi . '.id', '=', $siparisTabloAdi . '.durumId')
                ->leftJoin($islemTabloAdi, $islemTabloAdi . '.siparisId', '=', $siparisTabloAdi . '.id')
                ->leftJoin($kullaniciTabloAdi, $kullaniciTabloAdi . '.id', '=', $siparisTabloAdi . '.userId')
                ->groupBy(
                    $siparisTabloAdi . '.id',
                    $siparisTabloAdi . '.ad',
                    $siparisTabloAdi . '.irsaliyeNo',
                    $siparisTabloAdi . '.siparisNo',
                    $siparisTabloAdi . '.tarih',
                    $siparisTabloAdi . '.tutar',
                    $siparisTabloAdi . '.firmaId',
                    $siparisTabloAdi . '.durumId',
                    $siparisTabloAdi . '.terminSuresi',
                    $siparisTabloAdi . '.faturaKesildi',
                    $siparisTabloAdi . '.faturaTarihi',
                    $siparisTabloAdi . '.aciklama',
                    $siparisTabloAdi . '.bitisTarihi',
                    $siparisDurumTabloAdi . '.ad',
                    $firmaTabloAdi . '.firmaAdi',
                    $firmaTabloAdi . '.sorumluKisi',
                    $kullaniciTabloAdi . '.name',
                )->whereNull("$islemTabloAdi.deleted_at")
                ->orderBy($siparisTabloAdi . '.created_at', 'desc');
            // dd($siparisler);
            // ana sorgu sonrası
            $t3 = microtime(true);

            if (isset ($filtrelemeler["termin"]) && $filtrelemeler["termin"] > 0) {
                $tarih = Carbon::now()->subDays($filtrelemeler["termin"])->format('Y-m-d');
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", "<=", $tarih);
            }

            if (isset ($filtrelemeler["arama"]) && $filtrelemeler["arama"] != "") {
                // Sipariş no, firma adı, irsaliye no
                $siparisler = $siparisler->where(function ($query) use ($filtrelemeler, $siparisTabloAdi, $firmaTabloAdi) {
                    $query->where($siparisTabloAdi . '.siparisNo', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($firmaTabloAdi . '.firmaAdi', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($siparisTabloAdi . '.irsaliyeNo', 'like', '%' . $filtrelemeler["arama"] . '%');
                });
            }

            if (isset ($filtrelemeler["firma"]) && $filtrelemeler["firma"] && count($filtrelemeler["firma"]) > 0) {
                $firmaIdleri = array_column($filtrelemeler["firma"], "id");
                $siparisler = $siparisler->whereIn("$firmaTabloAdi.id", $firmaIdleri);
            }

            if (isset ($filtrelemeler["baslangicTarihi"]) && $filtrelemeler["baslangicTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", ">=", $filtrelemeler["baslangicTarihi"]);
            }

            if (isset ($filtrelemeler["bitisTarihi"]) && $filtrelemeler["bitisTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", "<=", $filtrelemeler["bitisTarihi"]);
            }

            if (isset ($filtrelemeler["faturaBaslangicTarihi"]) && $filtrelemeler["faturaBaslangicTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.faturaTarihi", ">=", $filtrelemeler["faturaBaslangicTarihi"]);
            }

            if (isset ($filtrelemeler["faturaBitisTarihi"]) && $filtrelemeler["faturaBitisTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.faturaTarihi", "<=", $filtrelemeler["faturaBitisTarihi"]);
            }

            if (isset ($filtrelemeler["faturaDurumu"]) && $filtrelemeler["faturaDurumu"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.faturaKesildi", $filtrelemeler["faturaDurumu"]);
            }

            if (isset ($filtrelemeler["siparisDurumu"]) && $filtrelemeler["siparisDurumu"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.durumId", $filtrelemeler["siparisDurumu"]);
            }

            if (isset ($filtrelemeler["tutar"]) && $filtrelemeler["tutar"] != "") {
                // if all currencies are equal to 0
                $siparisler = $siparisler->havingRaw("tutarTL = 0")
                    ->havingRaw("tutarUSD = 0")
                    ->havingRaw("tutarEURO = 0");
            }

            if (isset ($filtrelemeler["siparisId"]) && $filtrelemeler["siparisId"]) {
                $siparisler = $siparisler->where("$siparisTabloAdi.id", $filtrelemeler["siparisId"]);
            }

            // koşullar sonrası
            $t11 = microtime(true);

            $siparisler = $siparisler->paginate($sayfalamaSayisi)->toArray();
            // dd($siparisler);


            // döngü öncesi
            $t4 = microtime(true);
            foreach ($siparisler["data"] as &$siparis) {
                $terminBilgileri = $this->terminHesapla($siparis["tarih"], $siparis["terminSuresi"], $siparis["bitisTarihi"]);

                $siparis["gecenSure"] = $terminBilgileri["gecenSure"];
                $siparis["gecenSureRenk"] = $terminBilgileri["gecenSureRenk"];

                $siparis["tutarTLYazi"] = $this->yaziyaDonustur($siparis["tutarTL"], [
                    "paraBirimi" => $this->paraBirimleri["TL"],
                ]);
                $siparis["tutarUSDYazi"] = $this->yaziyaDonustur($siparis["tutarUSD"], [
                    "paraBirimi" => $this->paraBirimleri["USD"],
                ]);
                $siparis["tutarEUROYazi"] = $this->yaziyaDonustur($siparis["tutarEURO"], [
                    "paraBirimi" => $this->paraBirimleri["EURO"],
                ]);
                $siparis["netYazi"] = $this->yaziyaDonustur($siparis["net"], ["kg" => true]);
                $siparis["tarihTR"] = Carbon::parse($siparis["tarih"])->format("d.m.Y");
                $siparis["bitisTarihiTR"] = $siparis["bitisTarihi"] ? Carbon::parse($siparis["bitisTarihi"])->format("d.m.Y") : null;
                $siparis["faturaKesildi"] = $siparis["faturaKesildi"] == 1 ? true : false;
                $siparis["faturaKesildiYazi"] = $siparis["faturaKesildi"] ? "Kesildi" : "Kesilmedi";
            }
            // döngü sonrası
            $t5 = microtime(true);

            if ($cikti) {
                $siparisAlanlar = [
                    "siparisNo" => "Sipariş No",
                    "firmaAdi" => "Firma",
                    "tarihTR" => "Sipariş Tarihi",
                ];
                $islemlerAlanlar = [
                    "malzemeAdi" => "Malzeme",
                    "adet" => "Adet",
                    "miktar" => "Miktar",
                    "dara" => "Dara",
                    "daraSonraGirilecek" => "Dara Sonra Girilecek",
                    "net" => "Net Miktar",
                    "kalite" => "Kalite",
                    "islemTuruAdi" => "Yapılacak İşlem",
                    "istenilenSertlik" => "İst. Sertlik",
                ];

                if (auth()->user()->can('siparis_ucreti_goruntuleme')) {
                    $siparisAlanlar["tutarTL"] = "Tutar (₺)";
                    $siparisAlanlar["tutarUSD"] = "Tutar ($)";
                    $siparisAlanlar["tutarEURO"] = "Tutar (€)";
                    $islemlerAlanlar["birimFiyat"] = "Tutar";
                }

                if (auth()->user()->can("fatura_kesildi_listeleme")) {
                    $siparisAlanlar["faturaKesildiYazi"] = "Fatura Durumu";
                }

                $malzemeTabloAdi = (new Malzemeler())->getTable();
                $islemTuruTabloAdi = (new IslemTurleri())->getTable();
                $islemDurumuTabloAdi = (new IslemDurumlari())->getTable();

                foreach ($siparisler["data"] as &$siparis) {
                    $siparis["islemler"] = Islemler::selectRaw("
                            $islemTabloAdi.*,
                            ($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                            $malzemeTabloAdi.ad as malzemeAdi,
                            $islemDurumuTabloAdi.ad as islemDurumuAdi,
                            $islemTuruTabloAdi.ad as islemTuruAdi
                        ")
                        ->join($malzemeTabloAdi, "$malzemeTabloAdi.id", "$islemTabloAdi.malzemeId")
                        ->join($islemDurumuTabloAdi, "$islemDurumuTabloAdi.id", "$islemTabloAdi.durumId")
                        ->leftJoin($islemTuruTabloAdi, "$islemTuruTabloAdi.id", "$islemTabloAdi.islemTuruId")
                        ->where("$islemTabloAdi.siparisId", $siparis["siparisId"])
                        ->get()
                        ->toArray();

                    foreach ($siparis["islemler"] as &$islem) {
                        $islem["json"] = json_decode($islem["json"], true);
                        $islem["daraSonraGirilecek"] = $islem["json"] && $islem["json"]["daraSonraGirilecek"] ? "Evet" : "Hayır";
                    }
                }

                return (
                    new SiparisExcelExporter($siparisler["data"], [
                        "siparis" => $siparisAlanlar,
                        "islem" => $islemlerAlanlar
                    ])
                )->downloadExcel("Sipariş Listesi");
            }

            // Bitiş zamanı
            $t1 = microtime(true);
            $zamanlar = [
                "baslangic" => number_format($t0, '4', '.', ''),
                "sorguOncesi" => number_format($t2 - $t0, '4'),
                "sorguSonrasi" => number_format($t3 - $t0, '4'),
                "tabloAdlari" => number_format($t6 - $t0, '4'),
                "durumTabloAdlari" => number_format($t7 - $t0, '4'),
                "siparisTabloAdlari" => number_format($t8 - $t0, '4'),
                "islemTabloAdlari" => number_format($t9 - $t0, '4'),
                "kullaniciTabloAdlari" => number_format($t10 - $t0, '4'),
                "kosullarSonrasi" => number_format($t11 - $t0, '4'),
                "donguOncesi" => number_format($t4 - $t0, '4'),
                "donguSonrasi" => number_format($t5 - $t0, '4'),
                "bitis" => number_format($t1, '4', '.', ''),
            ];

            return response()->json([
                'durum' => true,
                'mesaj' => 'Siparişler başarıyla getirildi.',
                'zamanlar' => $zamanlar,
                'filtrelemeler' => $filtrelemeler,
                'siparisler' => $siparisler
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }

    }

    public function siparisler(Request $request)
    {
        // Başlangıç zamanı
        $t0 = microtime(true);
        try {
            $filtrelemeler = json_decode($request->filtreleme ?? "[]", true);

            $cikti = isset ($request->cikti) && json_decode($request->cikti) === true;

            $sayfalamaSayisi = $cikti ? 9999 : ($request->sayfalamaSayisi ?? 10);
            $firmaTabloAdi = (new Firmalar())->getTable();
            // Tablo adları zamanı
            $t6 = microtime(true);
            $siparisDurumTabloAdi = (new SiparisDurumlari())->getTable();
            // Durum Tablo adları zamanı
            $t7 = microtime(true);
            $siparisTabloAdi = (new Siparisler())->getTable();
            // Sipariş Tablo adları zamanı
            $t8 = microtime(true);
            $islemTabloAdi = (new Islemler())->getTable();
            // İşlem Tablo adları zamanı
            $t9 = microtime(true);
            $kullaniciTabloAdi = (new User())->getTable();
            // Kullanıcı Tablo adları zamanı
            $t10 = microtime(true);

            // bu kısım sipariş formu sayfasında hızlı değerler geitrmek için eklenmiştir
            // son 3/4 aylık siparişleri anasayfada gösterilmek üzere filtreleyip çeker
            // eğer arama yapılmışsa tüm kayıtlar arasından gelir
            $sorgulananAyMiktari = 4;
            $sorguKontrol = empty ($filtrelemeler['arama']) ? Carbon::now()->subMonths($sorgulananAyMiktari)->format('Y-m-d') : 0;

            $sorguKontrol = $request->page && ($request->page >= $request->last_page) ? 0 : $sorguKontrol;

            // ana sorgu öncesi
            $t2 = microtime(true);
            $siparisler = Siparisler::select(DB::raw("
                    $siparisTabloAdi.id as siparisId,
                    $siparisTabloAdi.ad as siparisAdi,
                    $siparisTabloAdi.irsaliyeNo,
                    $siparisTabloAdi.siparisNo,
                    $siparisTabloAdi.tarih,
                    $siparisTabloAdi.tutar,
                    $siparisTabloAdi.firmaId,
                    $siparisTabloAdi.durumId,
                    $siparisTabloAdi.terminSuresi,
                    $siparisTabloAdi.faturaKesildi,
                    $siparisTabloAdi.faturaTarihi,
                    $siparisTabloAdi.aciklama,
                    $siparisTabloAdi.bitisTarihi,
                    $siparisDurumTabloAdi.ad as siparisDurumAdi,
                    $firmaTabloAdi.firmaAdi,
                    $firmaTabloAdi.sorumluKisi,
                    $kullaniciTabloAdi.name as duzenleyen,
                    COUNT(IF($islemTabloAdi.deleted_at IS NULL AND $islemTabloAdi.bolunmusId IS NULL, $islemTabloAdi.id, NULL)) as islemSayisi,
                    SUM($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                    SUM(
                        IF(
                            $islemTabloAdi.paraBirimi = 'TL',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        )
                    ) as tutarTL,
                    SUM(
                        IF(
                            $islemTabloAdi.paraBirimi = 'USD',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        )
                    ) as tutarUSD,
                    SUM(
                        IF(
                            $islemTabloAdi.paraBirimi = 'EURO',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        )
                    ) as tutarEURO
                "))
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($siparisDurumTabloAdi, $siparisDurumTabloAdi . '.id', '=', $siparisTabloAdi . '.durumId')
                ->leftJoin($islemTabloAdi, $islemTabloAdi . '.siparisId', '=', $siparisTabloAdi . '.id')
                ->leftJoin($kullaniciTabloAdi, $kullaniciTabloAdi . '.id', '=', $siparisTabloAdi . '.userId')
                ->groupBy(
                    $siparisTabloAdi . '.id',
                    $siparisTabloAdi . '.ad',
                    $siparisTabloAdi . '.irsaliyeNo',
                    $siparisTabloAdi . '.siparisNo',
                    $siparisTabloAdi . '.tarih',
                    $siparisTabloAdi . '.tutar',
                    $siparisTabloAdi . '.firmaId',
                    $siparisTabloAdi . '.durumId',
                    $siparisTabloAdi . '.terminSuresi',
                    $siparisTabloAdi . '.faturaKesildi',
                    $siparisTabloAdi . '.faturaTarihi',
                    $siparisTabloAdi . '.aciklama',
                    $siparisTabloAdi . '.bitisTarihi',
                    $siparisDurumTabloAdi . '.ad',
                    $firmaTabloAdi . '.firmaAdi',
                    $firmaTabloAdi . '.sorumluKisi',
                    $kullaniciTabloAdi . '.name',
                );
            // dd($siparisler);

            $toplamSiparis = new Siparisler();
            $toplamSiparis = $toplamSiparis->select(DB::raw("COUNT($siparisTabloAdi.id) as toplamSiparis"))
                ->whereNull("$siparisTabloAdi.deleted_at")
                ->first()->toplamSiparis;
            // dd($toplamSiparis);

            // ana sorgu sonrası
            $t3 = microtime(true);

            if (isset ($filtrelemeler["termin"]) && $filtrelemeler["termin"] > 0) {
                $tarih = Carbon::now()->subDays($filtrelemeler["termin"])->format('Y-m-d');
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", "<=", $tarih);
            }

            if (isset ($filtrelemeler["arama"]) && $filtrelemeler["arama"] != "") {
                // Sipariş no, firma adı, irsaliye no
                $siparisler = $siparisler->where(function ($query) use ($filtrelemeler, $siparisTabloAdi, $firmaTabloAdi) {
                    $query->where($siparisTabloAdi . '.siparisNo', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($firmaTabloAdi . '.firmaAdi', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($siparisTabloAdi . '.irsaliyeNo', 'like', '%' . $filtrelemeler["arama"] . '%');
                });
            }

            if (isset ($filtrelemeler["firma"]) && $filtrelemeler["firma"] && count($filtrelemeler["firma"]) > 0) {
                $firmaIdleri = array_column($filtrelemeler["firma"], "id");
                $siparisler = $siparisler->whereIn("$firmaTabloAdi.id", $firmaIdleri);
            }

            if (isset ($filtrelemeler["baslangicTarihi"]) && $filtrelemeler["baslangicTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", ">=", $filtrelemeler["baslangicTarihi"]);
            }

            if (isset ($filtrelemeler["bitisTarihi"]) && $filtrelemeler["bitisTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", "<=", $filtrelemeler["bitisTarihi"]);
            }

            if (isset ($filtrelemeler["faturaBaslangicTarihi"]) && $filtrelemeler["faturaBaslangicTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.faturaTarihi", ">=", $filtrelemeler["faturaBaslangicTarihi"]);
            }

            if (isset ($filtrelemeler["faturaBitisTarihi"]) && $filtrelemeler["faturaBitisTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.faturaTarihi", "<=", $filtrelemeler["faturaBitisTarihi"]);
            }

            if (isset ($filtrelemeler["faturaDurumu"]) && $filtrelemeler["faturaDurumu"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.faturaKesildi", $filtrelemeler["faturaDurumu"]);
            }

            if (isset ($filtrelemeler["siparisDurumu"]) && $filtrelemeler["siparisDurumu"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.durumId", $filtrelemeler["siparisDurumu"]);
            }

            if (isset ($filtrelemeler["tutar"]) && $filtrelemeler["tutar"] != "") {
                // if all currencies are equal to 0
                $siparisler = $siparisler->havingRaw("tutarTL = 0")
                    ->havingRaw("tutarUSD = 0")
                    ->havingRaw("tutarEURO = 0");
            }

            if (isset ($filtrelemeler["siparisId"]) && $filtrelemeler["siparisId"]) {
                $siparisler = $siparisler->where("$siparisTabloAdi.id", $filtrelemeler["siparisId"]);
            }

            // koşullar sonrası
            $t11 = microtime(true);

            $siparisler = $siparisler->whereNull("$islemTabloAdi.deleted_at")
                ->whereDate('tarih', '>', $sorguKontrol)
                ->orderBy($siparisTabloAdi . '.created_at', 'desc')->paginate($sayfalamaSayisi)->toArray();
            // dd($siparisler);


            // döngü öncesi
            $t4 = microtime(true);
            foreach ($siparisler["data"] as &$siparis) {
                $terminBilgileri = $this->terminHesapla($siparis["tarih"], $siparis["terminSuresi"], $siparis["bitisTarihi"]);

                $siparis["gecenSure"] = $terminBilgileri["gecenSure"];
                $siparis["gecenSureRenk"] = $terminBilgileri["gecenSureRenk"];

                $siparis["tutarTLYazi"] = $this->yaziyaDonustur($siparis["tutarTL"], [
                    "paraBirimi" => $this->paraBirimleri["TL"],
                ]);
                $siparis["tutarUSDYazi"] = $this->yaziyaDonustur($siparis["tutarUSD"], [
                    "paraBirimi" => $this->paraBirimleri["USD"],
                ]);
                $siparis["tutarEUROYazi"] = $this->yaziyaDonustur($siparis["tutarEURO"], [
                    "paraBirimi" => $this->paraBirimleri["EURO"],
                ]);
                $siparis["netYazi"] = $this->yaziyaDonustur($siparis["net"], ["kg" => true]);
                $siparis["tarihTR"] = Carbon::parse($siparis["tarih"])->format("d.m.Y");
                $siparis["bitisTarihiTR"] = $siparis["bitisTarihi"] ? Carbon::parse($siparis["bitisTarihi"])->format("d.m.Y") : null;
                $siparis["faturaKesildi"] = $siparis["faturaKesildi"] == 1 ? true : false;
                $siparis["faturaKesildiYazi"] = $siparis["faturaKesildi"] ? "Kesildi" : "Kesilmedi";
            }
            // döngü sonrası
            $t5 = microtime(true);

            if ($cikti) {
                $siparisAlanlar = [
                    "siparisNo" => "Sipariş No",
                    "firmaAdi" => "Firma",
                    "tarihTR" => "Sipariş Tarihi",
                ];
                $islemlerAlanlar = [
                    "malzemeAdi" => "Malzeme",
                    "adet" => "Adet",
                    "miktar" => "Miktar",
                    "dara" => "Dara",
                    "daraSonraGirilecek" => "Dara Sonra Girilecek",
                    "net" => "Net Miktar",
                    "kalite" => "Kalite",
                    "islemTuruAdi" => "Yapılacak İşlem",
                    "istenilenSertlik" => "İst. Sertlik",
                ];

                if (auth()->user()->can('siparis_ucreti_goruntuleme')) {
                    $siparisAlanlar["tutarTL"] = "Tutar (₺)";
                    $siparisAlanlar["tutarUSD"] = "Tutar ($)";
                    $siparisAlanlar["tutarEURO"] = "Tutar (€)";
                    $islemlerAlanlar["birimFiyat"] = "Tutar";
                }

                if (auth()->user()->can("fatura_kesildi_listeleme")) {
                    $siparisAlanlar["faturaKesildiYazi"] = "Fatura Durumu";
                }

                $malzemeTabloAdi = (new Malzemeler())->getTable();
                $islemTuruTabloAdi = (new IslemTurleri())->getTable();
                $islemDurumuTabloAdi = (new IslemDurumlari())->getTable();

                foreach ($siparisler["data"] as &$siparis) {
                    $siparis["islemler"] = Islemler::selectRaw("
                            $islemTabloAdi.*,
                            ($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                            $malzemeTabloAdi.ad as malzemeAdi,
                            $islemDurumuTabloAdi.ad as islemDurumuAdi,
                            $islemTuruTabloAdi.ad as islemTuruAdi
                        ")
                        ->join($malzemeTabloAdi, "$malzemeTabloAdi.id", "$islemTabloAdi.malzemeId")
                        ->join($islemDurumuTabloAdi, "$islemDurumuTabloAdi.id", "$islemTabloAdi.durumId")
                        ->leftJoin($islemTuruTabloAdi, "$islemTuruTabloAdi.id", "$islemTabloAdi.islemTuruId")
                        ->where("$islemTabloAdi.siparisId", $siparis["siparisId"])
                        ->get()
                        ->toArray();

                    foreach ($siparis["islemler"] as &$islem) {
                        $islem["json"] = json_decode($islem["json"], true);
                        $islem["daraSonraGirilecek"] = $islem["json"] && $islem["json"]["daraSonraGirilecek"] ? "Evet" : "Hayır";
                    }
                }

                return (
                    new SiparisExcelExporter($siparisler["data"], [
                        "siparis" => $siparisAlanlar,
                        "islem" => $islemlerAlanlar
                    ])
                )->downloadExcel("Sipariş Listesi");
            }

            // Bitiş zamanı
            $t1 = microtime(true);
            $zamanlar = [
                "baslangic" => number_format($t0, '4', '.', ''),
                "sorguOncesi" => number_format($t2 - $t0, '4'),
                "sorguSonrasi" => number_format($t3 - $t0, '4'),
                "tabloAdlari" => number_format($t6 - $t0, '4'),
                "durumTabloAdlari" => number_format($t7 - $t0, '4'),
                "siparisTabloAdlari" => number_format($t8 - $t0, '4'),
                "islemTabloAdlari" => number_format($t9 - $t0, '4'),
                "kullaniciTabloAdlari" => number_format($t10 - $t0, '4'),
                "kosullarSonrasi" => number_format($t11 - $t0, '4'),
                "donguOncesi" => number_format($t4 - $t0, '4'),
                "donguSonrasi" => number_format($t5 - $t0, '4'),
                "bitis" => number_format($t1, '4', '.', ''),
            ];

            $siparisler["toplamSiparis"] = $toplamSiparis;

            return response()->json([
                'durum' => true,
                'mesaj' => 'Siparişler başarıyla getirildi.',
                'toplamSiparis' => $toplamSiparis,
                // 'zamanlar' => $zamanlar,
                // 'filtrelemeler' => $filtrelemeler,
                'siparisler' => $siparisler
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }

    }

    public function siparisler_yeni(Request $request)
    {
        // Başlangıç zamanı
        $t0 = microtime(true);
        try {
            $siparisler = new Siparisler();
            $firmalar = new Firmalar();
            $siparisDurumlari = new SiparisDurumlari();
            $islemler = new Islemler();
            $user = new User();
            $filtrelemeler = json_decode($request->filtreleme ?? "[]", true);

            $cikti = isset ($request->cikti) && json_decode($request->cikti) === true;

            $sayfalamaSayisi = $cikti ? 9999 : ($request->sayfalamaSayisi ?? 10);
            $firmaTabloAdi = $firmalar->getTable();
            // Tablo adları zamanı
            $t6 = microtime(true);
            $siparisDurumTabloAdi = $siparisDurumlari->getTable();
            // Durum Tablo adları zamanı
            $t7 = microtime(true);
            $siparisTabloAdi = $siparisler->getTable();
            // Sipariş Tablo adları zamanı
            $t8 = microtime(true);
            $islemTabloAdi = $islemler->getTable();
            // İşlem Tablo adları zamanı
            $t9 = microtime(true);
            $kullaniciTabloAdi = $user->getTable();
            // Kullanıcı Tablo adları zamanı
            $t10 = microtime(true);

            // ana sorgu öncesi
            $t2 = microtime(true);
            $siparis = $siparisler->select(
                "$siparisTabloAdi.ad as siparisAdi",
                "$siparisTabloAdi.id as siparisId",
                "$siparisTabloAdi.irsaliyeNo",
                "$siparisTabloAdi.siparisNo",
                "$siparisTabloAdi.tarih",
                "$siparisTabloAdi.tutar",
                "$siparisTabloAdi.firmaId",
                "$siparisTabloAdi.durumId",
                "$siparisTabloAdi.terminSuresi",
                "$siparisTabloAdi.faturaKesildi",
                "$siparisTabloAdi.faturaTarihi",
                "$siparisTabloAdi.aciklama",
                "$siparisTabloAdi.bitisTarihi",
                "$siparisDurumTabloAdi.ad as siparisDurumAdi"
            )->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($siparisDurumTabloAdi, $siparisDurumTabloAdi . '.id', '=', $siparisTabloAdi . '.durumId')
                ->leftJoin($islemTabloAdi, $islemTabloAdi . '.siparisId', '=', $siparisTabloAdi . '.id')
                ->leftJoin($kullaniciTabloAdi, $kullaniciTabloAdi . '.id', '=', $siparisTabloAdi . '.userId');

            dd($siparis->paginate($sayfalamaSayisi)->toArray());

            // mevcut sorgu
            $siparisler = Siparisler::select(
                "$siparisTabloAdi.id as siparisId,
                $siparisTabloAdi.ad as siparisAdi,
                $siparisTabloAdi.irsaliyeNo,
                $siparisTabloAdi.siparisNo,
                $siparisTabloAdi.tarih,
                $siparisTabloAdi.tutar,
                $siparisTabloAdi.firmaId,
                $siparisTabloAdi.durumId,
                $siparisTabloAdi.terminSuresi,
                $siparisTabloAdi.faturaKesildi,
                $siparisTabloAdi.faturaTarihi,
                $siparisTabloAdi.aciklama,
                $siparisTabloAdi.bitisTarihi,
                $siparisDurumTabloAdi.ad as siparisDurumAdi,
                $firmaTabloAdi.firmaAdi,
                $firmaTabloAdi.sorumluKisi,
                $kullaniciTabloAdi.name as duzenleyen",
                DB::raw("
                    COUNT(IF($islemTabloAdi.deleted_at IS NULL AND $islemTabloAdi.bolunmusId IS NULL, $islemTabloAdi.id, NULL)) as islemSayisi,
                    SUM($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                    SUM(
                        IF(
                            $islemTabloAdi.paraBirimi = 'TL',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        )
                    ) as tutarTL,
                    SUM(
                        IF(
                            $islemTabloAdi.paraBirimi = 'USD',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        )
                    ) as tutarUSD,
                    SUM(
                        IF(
                            $islemTabloAdi.paraBirimi = 'EURO',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        )
                    ) as tutarEURO
                ")
            )
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($siparisDurumTabloAdi, $siparisDurumTabloAdi . '.id', '=', $siparisTabloAdi . '.durumId')
                ->leftJoin($islemTabloAdi, $islemTabloAdi . '.siparisId', '=', $siparisTabloAdi . '.id')
                ->leftJoin($kullaniciTabloAdi, $kullaniciTabloAdi . '.id', '=', $siparisTabloAdi . '.userId')
                ->groupBy(
                    $siparisTabloAdi . '.id',
                    $siparisTabloAdi . '.ad',
                    $siparisTabloAdi . '.irsaliyeNo',
                    $siparisTabloAdi . '.siparisNo',
                    $siparisTabloAdi . '.tarih',
                    $siparisTabloAdi . '.tutar',
                    $siparisTabloAdi . '.firmaId',
                    $siparisTabloAdi . '.durumId',
                    $siparisTabloAdi . '.terminSuresi',
                    $siparisTabloAdi . '.faturaKesildi',
                    $siparisTabloAdi . '.faturaTarihi',
                    $siparisTabloAdi . '.aciklama',
                    $siparisTabloAdi . '.bitisTarihi',
                    $siparisDurumTabloAdi . '.ad',
                    $firmaTabloAdi . '.firmaAdi',
                    $firmaTabloAdi . '.sorumluKisi',
                    $kullaniciTabloAdi . '.name',
                )->whereNull("$islemTabloAdi.deleted_at")
                ->orderBy($siparisTabloAdi . '.created_at', 'desc');
            dd($siparisler->paginate($sayfalamaSayisi)->toArray());
            // ana sorgu sonrası
            $t3 = microtime(true);

            if (isset ($filtrelemeler["termin"]) && $filtrelemeler["termin"] > 0) {
                $tarih = Carbon::now()->subDays($filtrelemeler["termin"])->format('Y-m-d');
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", "<=", $tarih);
            }

            if (isset ($filtrelemeler["arama"]) && $filtrelemeler["arama"] != "") {
                // Sipariş no, firma adı, irsaliye no
                $siparisler = $siparisler->where(function ($query) use ($filtrelemeler, $siparisTabloAdi, $firmaTabloAdi) {
                    $query->where($siparisTabloAdi . '.siparisNo', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($firmaTabloAdi . '.firmaAdi', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($siparisTabloAdi . '.irsaliyeNo', 'like', '%' . $filtrelemeler["arama"] . '%');
                });
            }

            if (isset ($filtrelemeler["firma"]) && $filtrelemeler["firma"] && count($filtrelemeler["firma"]) > 0) {
                $firmaIdleri = array_column($filtrelemeler["firma"], "id");
                $siparisler = $siparisler->whereIn("$firmaTabloAdi.id", $firmaIdleri);
            }

            if (isset ($filtrelemeler["baslangicTarihi"]) && $filtrelemeler["baslangicTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", ">=", $filtrelemeler["baslangicTarihi"]);
            }

            if (isset ($filtrelemeler["bitisTarihi"]) && $filtrelemeler["bitisTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", "<=", $filtrelemeler["bitisTarihi"]);
            }

            if (isset ($filtrelemeler["faturaBaslangicTarihi"]) && $filtrelemeler["faturaBaslangicTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.faturaTarihi", ">=", $filtrelemeler["faturaBaslangicTarihi"]);
            }

            if (isset ($filtrelemeler["faturaBitisTarihi"]) && $filtrelemeler["faturaBitisTarihi"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.faturaTarihi", "<=", $filtrelemeler["faturaBitisTarihi"]);
            }

            if (isset ($filtrelemeler["faturaDurumu"]) && $filtrelemeler["faturaDurumu"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.faturaKesildi", $filtrelemeler["faturaDurumu"]);
            }

            if (isset ($filtrelemeler["siparisDurumu"]) && $filtrelemeler["siparisDurumu"] != "") {
                $siparisler = $siparisler->where("$siparisTabloAdi.durumId", $filtrelemeler["siparisDurumu"]);
            }

            if (isset ($filtrelemeler["tutar"]) && $filtrelemeler["tutar"] != "") {
                // if all currencies are equal to 0
                $siparisler = $siparisler->havingRaw("tutarTL = 0")
                    ->havingRaw("tutarUSD = 0")
                    ->havingRaw("tutarEURO = 0");
            }

            if (isset ($filtrelemeler["siparisId"]) && $filtrelemeler["siparisId"]) {
                $siparisler = $siparisler->where("$siparisTabloAdi.id", $filtrelemeler["siparisId"]);
            }

            // koşullar sonrası
            $t11 = microtime(true);

            $siparisler = $siparisler->paginate($sayfalamaSayisi)->toArray();
            // dd($siparisler);


            // döngü öncesi
            $t4 = microtime(true);
            foreach ($siparisler["data"] as &$siparis) {
                $terminBilgileri = $this->terminHesapla($siparis["tarih"], $siparis["terminSuresi"], $siparis["bitisTarihi"]);

                $siparis["gecenSure"] = $terminBilgileri["gecenSure"];
                $siparis["gecenSureRenk"] = $terminBilgileri["gecenSureRenk"];

                $siparis["tutarTLYazi"] = $this->yaziyaDonustur($siparis["tutarTL"], [
                    "paraBirimi" => $this->paraBirimleri["TL"],
                ]);
                $siparis["tutarUSDYazi"] = $this->yaziyaDonustur($siparis["tutarUSD"], [
                    "paraBirimi" => $this->paraBirimleri["USD"],
                ]);
                $siparis["tutarEUROYazi"] = $this->yaziyaDonustur($siparis["tutarEURO"], [
                    "paraBirimi" => $this->paraBirimleri["EURO"],
                ]);
                $siparis["netYazi"] = $this->yaziyaDonustur($siparis["net"], ["kg" => true]);
                $siparis["tarihTR"] = Carbon::parse($siparis["tarih"])->format("d.m.Y");
                $siparis["bitisTarihiTR"] = $siparis["bitisTarihi"] ? Carbon::parse($siparis["bitisTarihi"])->format("d.m.Y") : null;
                $siparis["faturaKesildi"] = $siparis["faturaKesildi"] == 1 ? true : false;
                $siparis["faturaKesildiYazi"] = $siparis["faturaKesildi"] ? "Kesildi" : "Kesilmedi";
            }
            // döngü sonrası
            $t5 = microtime(true);

            if ($cikti) {
                $siparisAlanlar = [
                    "siparisNo" => "Sipariş No",
                    "firmaAdi" => "Firma",
                    "tarihTR" => "Sipariş Tarihi",
                ];
                $islemlerAlanlar = [
                    "malzemeAdi" => "Malzeme",
                    "adet" => "Adet",
                    "miktar" => "Miktar",
                    "dara" => "Dara",
                    "daraSonraGirilecek" => "Dara Sonra Girilecek",
                    "net" => "Net Miktar",
                    "kalite" => "Kalite",
                    "islemTuruAdi" => "Yapılacak İşlem",
                    "istenilenSertlik" => "İst. Sertlik",
                ];

                if (auth()->user()->can('siparis_ucreti_goruntuleme')) {
                    $siparisAlanlar["tutarTL"] = "Tutar (₺)";
                    $siparisAlanlar["tutarUSD"] = "Tutar ($)";
                    $siparisAlanlar["tutarEURO"] = "Tutar (€)";
                    $islemlerAlanlar["birimFiyat"] = "Tutar";
                }

                if (auth()->user()->can("fatura_kesildi_listeleme")) {
                    $siparisAlanlar["faturaKesildiYazi"] = "Fatura Durumu";
                }

                $malzemeTabloAdi = (new Malzemeler())->getTable();
                $islemTuruTabloAdi = (new IslemTurleri())->getTable();
                $islemDurumuTabloAdi = (new IslemDurumlari())->getTable();

                foreach ($siparisler["data"] as &$siparis) {
                    $siparis["islemler"] = Islemler::selectRaw("
                            $islemTabloAdi.*,
                            ($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                            $malzemeTabloAdi.ad as malzemeAdi,
                            $islemDurumuTabloAdi.ad as islemDurumuAdi,
                            $islemTuruTabloAdi.ad as islemTuruAdi
                        ")
                        ->join($malzemeTabloAdi, "$malzemeTabloAdi.id", "$islemTabloAdi.malzemeId")
                        ->join($islemDurumuTabloAdi, "$islemDurumuTabloAdi.id", "$islemTabloAdi.durumId")
                        ->leftJoin($islemTuruTabloAdi, "$islemTuruTabloAdi.id", "$islemTabloAdi.islemTuruId")
                        ->where("$islemTabloAdi.siparisId", $siparis["siparisId"])
                        ->get()
                        ->toArray();

                    foreach ($siparis["islemler"] as &$islem) {
                        $islem["json"] = json_decode($islem["json"], true);
                        $islem["daraSonraGirilecek"] = $islem["json"] && $islem["json"]["daraSonraGirilecek"] ? "Evet" : "Hayır";
                    }
                }

                return (
                    new SiparisExcelExporter($siparisler["data"], [
                        "siparis" => $siparisAlanlar,
                        "islem" => $islemlerAlanlar
                    ])
                )->downloadExcel("Sipariş Listesi");
            }

            // Bitiş zamanı
            $t1 = microtime(true);
            $zamanlar = [
                "baslangic" => number_format($t0, '4', '.', ''),
                "sorguOncesi" => number_format($t2 - $t0, '4'),
                "sorguSonrasi" => number_format($t3 - $t0, '4'),
                "tabloAdlari" => number_format($t6 - $t0, '4'),
                "durumTabloAdlari" => number_format($t7 - $t0, '4'),
                "siparisTabloAdlari" => number_format($t8 - $t0, '4'),
                "islemTabloAdlari" => number_format($t9 - $t0, '4'),
                "kullaniciTabloAdlari" => number_format($t10 - $t0, '4'),
                "kosullarSonrasi" => number_format($t11 - $t0, '4'),
                "donguOncesi" => number_format($t4 - $t0, '4'),
                "donguSonrasi" => number_format($t5 - $t0, '4'),
                "bitis" => number_format($t1, '4', '.', ''),
            ];

            return response()->json([
                'durum' => true,
                'mesaj' => 'Siparişler başarıyla getirildi.',
                'zamanlar' => $zamanlar,
                'filtrelemeler' => $filtrelemeler,
                'siparisler' => $siparisler
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }

    }


    /**
     * Son sipariş ve irsaliye numarasının bir sonraki numarasını döndürür.
     * Örnek: DRK000001 -> DRK000002
     */
    public function numaralariGetir(Request $request)
    {
        try {
            $siparisNo = Siparisler::max('siparisNo');

            if (!$siparisNo) {
                $siparisNo = 'DRK0000001';
            } else {
                $siparisNo = substr($siparisNo, 3);
                $siparisNo = 'DRK' . sprintf('%07d', $siparisNo + 1);
            }

            $irsaliyeNo = Siparisler::max('irsaliyeNo');

            if (!$irsaliyeNo) {
                $irsaliyeNo = 'IR0000001';
            } else {
                $irsaliyeNo = substr($irsaliyeNo, 2);
                $irsaliyeNo = 'IR' . sprintf('%07d', $irsaliyeNo + 1);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş başarıyla getirildi.',
                'numaralar' => [
                    "siparisNo" => $siparisNo,
                    "irsaliyeNo" => $irsaliyeNo
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @global
     */
    public function siparisDurumlariGetir()
    {
        try {
            $siparisDurumlari = SiparisDurumlari::all();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş durumları başarıyla getirildi.',
                'siparisDurumlari' => $siparisDurumlari
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }

    public function siparisKaydet(Request $request)
    {
        DB::beginTransaction();

        try {
            $siparisBilgileri = $request->siparis;
            $userId = Auth::user()->id;
            // dd($siparisBilgileri);

            if (isset ($siparisBilgileri['siparisId'])) {
                $siparis = Siparisler::find($siparisBilgileri['siparisId']);

                if ($siparis->siparisNo != $siparisBilgileri['siparisNo'] && Siparisler::where('siparisNo', $siparisBilgileri['siparisNo'])->count() > 0) {
                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'Bu sipariş numarası zaten kullanılıyor.',
                        "hataKodu" => "SK001"
                    ]);
                }
            } else {
                $siparis = new Siparisler();

                if (Siparisler::where('siparisNo', $siparisBilgileri['siparisNo'])->count() > 0) {
                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'Bu sipariş numarası zaten kullanılıyor.',
                        "hataKodu" => "SK002"
                    ]);
                }
            }

            // dd($siparis);

            $siparis->firmaId = $siparisBilgileri['firma']["id"];
            $siparis->userId = $userId;
            $siparis->durumId = $siparisBilgileri['siparisDurumu']["id"];
            $siparis->ad = $siparisBilgileri['siparisAdi'];
            $siparis->siparisNo = $siparisBilgileri['siparisNo'];
            $siparis->irsaliyeNo = $siparisBilgileri['irsaliyeNo'];
            $siparis->aciklama = $siparisBilgileri['aciklama'] ?? null;
            $siparis->tarih = $siparisBilgileri['tarih'];
            $siparis->tutar = $siparisBilgileri['tutar'] ?? null;
            $siparis->terminSuresi = $siparisBilgileri['terminSuresi'] ?? 5;
            if ($siparisBilgileri['faturaKesildi']) {
                $siparis->faturaKesildi = $siparisBilgileri['faturaKesildi'];
                $siparis->faturaTarihi = isset ($siparisBilgileri['faturaTarihi']) && $siparisBilgileri['faturaTarihi'] ? $siparisBilgileri['faturaTarihi'] : now();
            } else {
                $siparis->faturaKesildi = false;
                $siparis->faturaTarihi = null;

            }


            if (!$siparis->save()) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Sipariş kaydedilirken bir hata oluştu.',
                    'hata' => $siparis->getErrors(),
                    "hataKodu" => "S001"
                ], 500);
            }

            $this->siparisDurumKontrol($siparis->id);

            foreach ($siparisBilgileri['islemler'] as $key => $islem) {
                if (isset ($islem['id'])) {
                    $islemModel = Islemler::find($islem['id']);
                } else {
                    $islemModel = new Islemler();
                }

                $islemJson = $islem["json"] ?? null;

                if ($islemJson === null) {
                    if (isset ($islem["daraSonraGirilecek"]) && $islem["daraSonraGirilecek"]) {
                        $islemJson = [
                            "daraSonraGirilecek" => true,
                        ];
                    }
                } else {
                    $islemJson = [
                        ...$islemJson,
                        "daraSonraGirilecek" => $islem["daraSonraGirilecek"] ?? false
                    ];
                }

                // dd($islemModel->siparisId, $siparisIslemleri, $siparis->id);

                $islemModel->siparisId = $siparis->id;
                $islemModel->malzemeId = $islem['malzeme']["id"] ?? null;
                $islemModel->islemTuruId = $islem['yapilacakIslem']["id"] ?? null;
                $islemModel->durumId = $islem['islemDurumu']["id"] ?? null;
                $islemModel->siraNo = $key + 1;
                $islemModel->adet = $islem['adet'] ?? 1;
                $islemModel->miktar = $islem['miktar'];
                $islemModel->dara = $islem['dara'];
                $islemModel->birimFiyat = $islem['birimFiyat'];
                $islemModel->paraBirimi = $islem['paraBirimi']["kod"] ?? "TL";
                $islemModel->miktarFiyatCarp = $islem['miktarFiyatCarp'] ?? 1;
                $islemModel->kalite = $islem['kalite'];
                $islemModel->istenilenSertlik = $islem['istenilenSertlik'];
                $islemModel->json = $islemJson !== null ? json_encode($islemJson) : null;

                if (isset ($islem["yeniResimSecildi"], $islem["resim"]) && $islem["yeniResimSecildi"] && $islem["resim"]) {
                    if (isset ($islem["resimYolu"]) && $islem["resimYolu"]) {
                        $this->dosyaSil($islem["resimYolu"]);
                    }

                    $resimYolu = $this->base64ResimKaydet($islem["resim"], [
                        "dosyaAdi" => "$siparis->siparisNo-$islemModel->siraNo"
                    ]);

                    if (!$resimYolu) {
                        DB::rollBack();

                        return response()->json([
                            'durum' => false,
                            'mesaj' => 'Resim kaydedilirken bir hata oluştu.',
                            "hataKodu" => "S003"
                        ], 500);
                    }

                    $islemModel->resimYolu = $resimYolu;
                }

                if (!$islemModel->save()) {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem kaydedilirken bir hata oluştu.',
                        'hata' => $islemModel->getErrors(),
                        "hataKodu" => "S002"
                    ], 500);
                }
            }

            if (isset ($siparisBilgileri['silinenIslemler']) && $siparisBilgileri['silinenIslemler']) {
                foreach ($siparisBilgileri['silinenIslemler'] as $islemId) {
                    $islemModel = Islemler::where("id", $islemId)->first();

                    // Eğer resimYolu varsa sil
                    if ($islemModel->resimYolu) {
                        $resimSilmeDurum = $this->dosyaSil($islemModel->resimYolu);

                        if (!$resimSilmeDurum) {
                            DB::rollBack();

                            return response()->json([
                                'durum' => false,
                                'mesaj' => 'Resim silinirken bir hata oluştu.',
                                "hataKodu" => "S004"
                            ], 500);
                        }
                    }

                    if (!$islemModel->delete()) {
                        DB::rollBack();

                        return response()->json([
                            'durum' => false,
                            'mesaj' => 'İşlem silinirken bir hata oluştu.',
                            'hata' => $islemModel->getErrors(),
                            "hataKodu" => "S005"
                        ], 500);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş başarıyla kaydedildi.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                'satir' => $e->getLine(),
            ], 500);
        }
    }

    public function siparisDetay(Request $request)
    {
        try {
            $siparisId = $request->siparisId;
            $siparisDetaylariGetir = $request->detaylariGetir;

            $islemler = Islemler::where('siparisId', $siparisId)->get();

            $donecekVeriler = [
                "islemler" => $islemler,
            ];

            if ($siparisDetaylariGetir) {
                $firmaTabloAdi = (new Firmalar())->getTable();
                $siparisDurumTabloAdi = (new SiparisDurumlari())->getTable();
                $siparisTabloAdi = (new Siparisler())->getTable();

                $siparisDetaylari = Siparisler::select("$siparisTabloAdi.*", $firmaTabloAdi . ".firmaAdi", "$firmaTabloAdi.sorumluKisi", $siparisDurumTabloAdi . ".ad as siparisDurumAdi")
                    ->join($firmaTabloAdi, $firmaTabloAdi . ".id", "=", "$siparisTabloAdi.firmaId")
                    ->join($siparisDurumTabloAdi, $siparisDurumTabloAdi . ".id", "=", "$siparisTabloAdi.durumId")
                    ->where("$siparisTabloAdi.id", $siparisId)
                    ->first()
                    ->toArray();

                $terminBilgileri = $this->terminHesapla($siparisDetaylari["tarih"], $siparisDetaylari["terminSuresi"]);

                $siparisDetaylari["gecenSure"] = $terminBilgileri["gecenSure"];
                $siparisDetaylari["gecenSureRenk"] = $terminBilgileri["gecenSureRenk"];

                $donecekVeriler["siparisDetaylari"] = $siparisDetaylari;
            }

            $siparisTarihi = Siparisler::where('id', $siparisId)->first()->tarih;

            $bolunmusToplamliIslemler = [];

            foreach ($donecekVeriler["islemler"] as $islem) {
                $paraBirimi = $this->paraBirimleri[$islem["paraBirimi"]];
                $islem["miktarYazi"] = $this->yaziyaDonustur($islem["miktar"], ["kg" => true]);
                $islem["daraYazi"] = $this->yaziyaDonustur($islem["dara"], ["kg" => true]);
                $islem["birimFiyatYazi"] = $this->yaziyaDonustur($islem["birimFiyat"], [
                    "paraBirimi" => $paraBirimi
                ]);
                $islem["paraBirimi"] = $paraBirimi;

                $islem["json"] = json_decode($islem["json"], true);

                $islem["daraSonraGirilecek"] = $islem["json"] && $islem["json"]["daraSonraGirilecek"];
                $islem["miktarFiyatCarp"] = $islem["miktarFiyatCarp"] == 1 ? true : false;

                $islem["bitisTarihiTR"] = Carbon::parse($islem["bitisTarihi"])->format("d.m.Y");

                $islem["islemTermini"] = $islem["durumId"] == "3" ? Carbon::parse($siparisTarihi)->diffInDays(Carbon::parse($islem["bitisTarihi"])) : null;


                $id = $islem["bolunmusId"] ? $islem["bolunmusId"] : $islem["id"];

                if (!isset ($bolunmusToplamliIslemler[$id])) {
                    $bolunmusToplamliIslemler[$id] = [];
                }

                $bolunmusToplamliIslemler[$id][] = $islem;
            }

            $donecekVeriler["bolunmusToplamliIslemler"] = [];

            foreach ($bolunmusToplamliIslemler as $bolunmusIslemId => $_islemler) {
                $anaIslemIndex = -1;
                $toplamlar = [
                    "miktar" => 0,
                    "dara" => 0,
                    "sonSertlik" => "0",
                ];
                foreach ($_islemler as $index => $islem) {
                    $toplamlar["miktar"] += (float) $islem["miktar"];
                    $toplamlar["dara"] += (float) $islem["dara"];
                    $sonSertlik = (float) $islem["sonSertlik"];

                    if ($toplamlar["sonSertlik"] == "0") {
                        $toplamlar["sonSertlik"] = $sonSertlik;
                    } else {
                        $toplamlar["sonSertlik"] .= "," . $sonSertlik;
                    }
                    if ($bolunmusIslemId == $islem["id"]) {
                        $anaIslemIndex = $index;
                    }
                }

                if ($anaIslemIndex > -1) {
                    // Referans bağlantısı koparılıyor
                    $anaIslem = json_decode(json_encode($_islemler[$anaIslemIndex]), true);

                    $anaIslem["miktar"] = $toplamlar["miktar"];
                    $anaIslem["dara"] = $toplamlar["dara"];
                    $anaIslem["sonSertlik"] = $toplamlar["sonSertlik"];

                    $anaIslem["miktarYazi"] = $this->yaziyaDonustur($anaIslem["miktar"], ["kg" => true]);
                    $anaIslem["daraYazi"] = $this->yaziyaDonustur($anaIslem["dara"], ["kg" => true]);

                    $donecekVeriler["bolunmusToplamliIslemler"][] = $anaIslem;
                }
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş başarıyla getirildi.',
                'veriler' => $donecekVeriler,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }

    public function siparisSil(Request $request)
    {
        DB::beginTransaction();
        try {
            $siparisId = $request->siparisId;

            $islemler = Islemler::where('siparisId', $siparisId)->get();

            foreach ($islemler as $islem) {
                // Eğer resimYolu varsa sil
                if ($islem->resimYolu) {
                    $resimSilmeDurum = $this->dosyaSil($islem->resimYolu);

                    if (!$resimSilmeDurum) {
                        DB::rollBack();

                        return response()->json([
                            'durum' => false,
                            'mesaj' => 'Resim silinirken bir hata oluştu.',
                            "hataKodu" => "S008"
                        ], 500);
                    }
                }

                if (!$islem->delete()) {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem silinirken bir hata oluştu.',
                        'hata' => $islem->getErrors(),
                        "hataKodu" => "S010"
                    ], 500);
                }
            }

            $siparis = Siparisler::find($siparisId);

            if (!$siparis) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Sipariş bulunamadı.',
                    "hataKodu" => "S011"
                ], 404);
            }

            if (!$siparis->delete()) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Sipariş silinirken bir hata oluştu.',
                    'hata' => $siparis->getErrors(),
                    "hataKodu" => "S012"
                ], 500);
            }

            DB::commit();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş başarıyla silindi.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                'satir' => $e->getLine(),
            ], 500);
        }
    }

    public function toplamSiparis()
    {
        try {
            $toplamSiparis = Siparisler::count();

            return response()->json([
                "durum" => true,
                "mesaj" => "Toplam sipariş sayısı bulundu.",
                "toplamSiparis" => $toplamSiparis,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "durum" => false,
                "mesaj" => "Toplam sipariş sayısı bulunurken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }
}
