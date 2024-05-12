<?php

namespace App\Http\Controllers;

use App\Models\Firinlar;
use App\Models\Firmalar;
use App\Models\Islemler;
use App\Models\IslemTurleri;
use App\Models\Siparisler;
use Illuminate\Http\Request;

class RaporlamaController extends Controller
{
    public function index()
    {
        $sonSiparisYili = Siparisler::selectRaw("YEAR(tarih) as yil")->groupBy('yil')->orderBy('yil', 'desc')->first();
        $ilkSiparisYili = Siparisler::selectRaw("YEAR(tarih) as yil")->groupBy('yil')->orderBy('yil', 'asc')->first();

        $firinlar = Firinlar::all();

        return view('raporlama', [
            'ilkSiparisYili' => $ilkSiparisYili->yil,
            'sonSiparisYili' => $sonSiparisYili->yil,
            "firinlar" => $firinlar,
        ]);
    }

    public function yillikCiroGetir()
    {
        try {
            $islemTabloAdi = (new Islemler())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();

            $yillikCiro = Siparisler::selectRaw("
                    YEAR(tarih) as yil,
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
                    ) as ciroTL,
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
                    ) as ciroUSD,
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
                    ) as ciroEURO
                ")
                ->join($islemTabloAdi, $islemTabloAdi . '.siparisId', '=', $siparisTabloAdi . '.id')
                ->groupBy('yil')
                ->get();

            // dd($yillikCiro->toArray());
            $yillar = $yillikCiro->pluck('yil')->toArray();
            $ciroTL = $yillikCiro->pluck('ciroTL')->toArray();
            $ciroUSD = $yillikCiro->pluck('ciroUSD')->toArray();
            $ciroEURO = $yillikCiro->pluck('ciroEURO')->toArray();

            return response()->json([
                "durum" => true,
                "mesaj" => "Yillik ciro getirildi",
                "yillikCiro" => [
                    "ciro" => [
                        "TL" => $ciroTL,
                        "USD" => $ciroUSD,
                        "EURO" => $ciroEURO,
                    ],
                    "yillar" => $yillar,
                    "tumu" => $yillikCiro->toArray(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "durum" => false,
                "mesaj" => "Yillik ciro getirilemedi",
                "hata" => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "YC_CATCH",
            ]);
        }
    }

    public function aylikCiroGetir(Request $request)
    {
        try {
            $yil = $request->yil ?? date('Y');

            $islemTabloAdi = (new Islemler())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();

            $aylikCiro = Siparisler::selectRaw("
                    MONTH($siparisTabloAdi.tarih) as ay,
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
                    ) as ciroTL,
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
                    ) as ciroUSD,
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
                    ) as ciroEURO
                ")
                ->join($islemTabloAdi, $islemTabloAdi . '.siparisId', '=', $siparisTabloAdi . '.id')
                ->whereYear("$siparisTabloAdi.tarih", $yil)
                ->groupBy('ay')
                ->get();

            // dd($aylikCiro->toArray());
            $aylar = $aylikCiro->pluck('ay')->toArray();
            $ciroTL = $aylikCiro->pluck('ciroTL')->toArray();
            $ciroUSD = $aylikCiro->pluck('ciroUSD')->toArray();
            $ciroEURO = $aylikCiro->pluck('ciroEURO')->toArray();

            $ayIsimleri = [
                1 => 'Oca',
                2 => 'Şub',
                3 => 'Mar',
                4 => 'Nis',
                5 => 'May',
                6 => 'Haz',
                7 => 'Tem',
                8 => 'Ağu',
                9 => 'Eyl',
                10 => 'Eki',
                11 => 'Kas',
                12 => 'Ara',
            ];

            foreach ($aylar as &$ay) {
                $ay = $ayIsimleri[$ay];
            }

            return response()->json([
                "durum" => true,
                "mesaj" => "Aylik ciro getirildi",
                "aylikCiro" => [
                    "ciro" => [
                        "TL" => $ciroTL,
                        "USD" => $ciroUSD,
                        "EURO" => $ciroEURO,
                    ],
                    "aylar" => $aylar,
                    "tumu" => $aylikCiro->toArray(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "durum" => false,
                "mesaj" => "Aylik ciro getirilemedi",
                "hata" => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "AC_CATCH",
            ]);
        }
    }

    public function firinBazliTonaj(Request $request)
    {
        try {
            $baslangicTarihi = $request->baslangicTarihi;
            $bitisTarihi = $request->bitisTarihi;
            $orderTuru = $request->orderTuru ?? "tonaj";

            $firinTabloAdi = (new Firinlar())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

            $firinlar = Firinlar::selectRaw("
                $firinTabloAdi.id,
                $firinTabloAdi.ad,
                $firinTabloAdi.kod,
                $firinTabloAdi.json,
                SUM($islemTabloAdi.miktar - $islemTabloAdi.dara) as tonaj,
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
                ->join($islemTabloAdi, "$firinTabloAdi.id", '=', "$islemTabloAdi.firinId")
                ->groupBy(
                    "$firinTabloAdi.id",
                    "$firinTabloAdi.ad",
                    "$firinTabloAdi.kod",
                    "$firinTabloAdi.json",
                )
                ->orderBy($orderTuru, 'desc');

            if ($baslangicTarihi) {
                $firinlar = $firinlar->where("$islemTabloAdi.created_at", '>=', $baslangicTarihi);

                if ($bitisTarihi) {
                    $firinlar = $firinlar->where("$islemTabloAdi.created_at", '<=', $bitisTarihi);
                }
            } else if ($bitisTarihi) {
                $firinlar = $firinlar->where("$islemTabloAdi.created_at", '<=', $bitisTarihi);
            }

            $firinlar = $firinlar->get();

            $toplamlar = [
                "tonaj" => 0,
                "tutarTL" => 0,
                "tutarUSD" => 0,
                "tutarEURO" => 0,
                "firinSayisi" => 0,
            ];
            foreach ($firinlar as &$firin) {
                $firin->tutarTLYazi = $this->yaziyaDonustur($firin->tutarTL, [
                    "paraBirimi" => $this->paraBirimleri["TL"],
                ]);
                $firin->tutarUSDYazi = $this->yaziyaDonustur($firin->tutarUSD, [
                    "paraBirimi" => $this->paraBirimleri["USD"],
                ]);
                $firin->tutarEUROYazi = $this->yaziyaDonustur($firin->tutarEURO, [
                    "paraBirimi" => $this->paraBirimleri["EURO"],
                ]);
                $firin->tonajYazi = $this->yaziyaDonustur($firin->tonaj, [
                    "kg" => true,
                ]);

                $firin->json = json_decode($firin->json);

                $toplamlar["tonaj"] += $firin->tonaj;
                $toplamlar["tutarTL"] += $firin->tutarTL;
                $toplamlar["tutarUSD"] += $firin->tutarUSD;
                $toplamlar["tutarEURO"] += $firin->tutarEURO;
                $toplamlar["firinSayisi"]++;
            }

            $toplamlar["tonajYazi"] = $this->yaziyaDonustur($toplamlar["tonaj"], [
                "kg" => true,
            ]);
            $toplamlar["tutarTLYazi"] = $this->yaziyaDonustur($toplamlar["tutarTL"], [
                "paraBirimi" => $this->paraBirimleri["TL"],
            ]);
            $toplamlar["tutarUSDYazi"] = $this->yaziyaDonustur($toplamlar["tutarUSD"], [
                "paraBirimi" => $this->paraBirimleri["USD"],
            ]);
            $toplamlar["tutarEUROYazi"] = $this->yaziyaDonustur($toplamlar["tutarEURO"], [
                "paraBirimi" => $this->paraBirimleri["EURO"],
            ]);

            return response()->json([
                "durum" => true,
                "mesaj" => "Firin bazli tonaj getirildi",
                "firinlar" => $firinlar->toArray(),
                "toplamlar" => $toplamlar,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "durum" => false,
                "mesaj" => "Firin bazli tonaj getirilemedi",
                "hata" => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "FB_CATCH",
            ]);
        }
    }

    public function firmaBazliBilgileriGetir(Request $request)
    {
        try {
            $baslangicTarihi = $request->baslangicTarihi;
            $bitisTarihi = $request->bitisTarihi;
            $arama = $request->arama;
            $orderTuru = $request->orderTuru ?? "tonaj";

            $firmaTabloAdi = (new Firmalar())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();

            $firmalar = Firmalar::selectRaw("
                $firmaTabloAdi.id,
                $firmaTabloAdi.firmaAdi,
                $firmaTabloAdi.sorumluKisi,
                SUM($islemTabloAdi.miktar - $islemTabloAdi.dara) as tonaj,
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
                ->join($siparisTabloAdi, "$firmaTabloAdi.id", '=', "$siparisTabloAdi.firmaId")
                ->join($islemTabloAdi, "$siparisTabloAdi.id", '=', "$islemTabloAdi.siparisId")
                ->groupBy(
                    "$firmaTabloAdi.id",
                    "$firmaTabloAdi.firmaAdi",
                    "$firmaTabloAdi.sorumluKisi",
                )
                ->orderBy($orderTuru, 'desc');

            if ($arama) {
                $firmalar = $firmalar->where("$firmaTabloAdi.firmaAdi", 'like', "%$arama%")
                    ->orWhere("$firmaTabloAdi.sorumluKisi", 'like', "%$arama%");
            }

            if ($baslangicTarihi) {
                $firmalar = $firmalar->where("$islemTabloAdi.created_at", '>=', $baslangicTarihi);

                if ($bitisTarihi) {
                    $firmalar = $firmalar->where("$islemTabloAdi.created_at", '<=', $bitisTarihi);
                }
            } else if ($bitisTarihi) {
                $firmalar = $firmalar->where("$islemTabloAdi.created_at", '<=', $bitisTarihi);
            }

            $firmalar = $firmalar->paginate(6);

            foreach ($firmalar as &$firma) {
                $firma->tonajYazi = $this->yaziyaDonustur($firma->tonaj, [
                    "kg" => true,
                ]);
                $firma->tutarTLYazi = $this->yaziyaDonustur($firma->tutarTL, [
                    "paraBirimi" => $this->paraBirimleri["TL"],
                ]);
                $firma->tutarUSDYazi = $this->yaziyaDonustur($firma->tutarUSD, [
                    "paraBirimi" => $this->paraBirimleri["USD"],
                ]);
                $firma->tutarEUROYazi = $this->yaziyaDonustur($firma->tutarEURO, [
                    "paraBirimi" => $this->paraBirimleri["EURO"],
                ]);
            }

            return response()->json([
                "durum" => true,
                "mesaj" => "Firma bazli bilgiler getirildi",
                "firmalar" => $firmalar->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "durum" => false,
                "mesaj" => "Firma bazli bilgiler getirilemedi",
                "hata" => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "FBB_CATCH",
            ]);
        }
    }

    public function firinBazliIslemTurleriGetir(Request $request)
    {
        try {
            $baslangicTarihi = $request->baslangicTarihi;
            $bitisTarihi = $request->bitisTarihi;
            $orderTuru = $request->orderTuru ?? "toplam";
            $islemTabloAdi = (new Islemler())->getTable();
            $firinTabloAdi = (new Firinlar())->getTable();
            $islemTurleriTabloAdi = (new IslemTurleri())->getTable();

            $islemTurleri = Firinlar::selectRaw("
                $firinTabloAdi.id,
                $firinTabloAdi.ad,
                $firinTabloAdi.kod,
                $firinTabloAdi.json,
                $islemTabloAdi.islemTuruId,
                $islemTurleriTabloAdi.ad as islemTuruAdi,
                GROUP_CONCAT($islemTabloAdi.sarj) as sarjlar,
                COUNT($islemTurleriTabloAdi.id) as toplam,
                COUNT($islemTabloAdi.tekrarEdenId) as toplamTekrarEden
            ")->join($islemTabloAdi, "$firinTabloAdi.id", '=', "$islemTabloAdi.firinId")
                ->join($islemTurleriTabloAdi, "$islemTabloAdi.islemTuruId", '=', "$islemTurleriTabloAdi.id")
                ->groupBy(
                    "$islemTabloAdi.islemTuruId",
                    "$islemTurleriTabloAdi.ad",
                    "$firinTabloAdi.id",
                    "$firinTabloAdi.ad",
                    "$firinTabloAdi.kod",
                    "$firinTabloAdi.json",
                )->orderBy($orderTuru, 'desc');

            if ($baslangicTarihi) {
                $islemTurleri = $islemTurleri->where("$islemTabloAdi.created_at", '>=', $baslangicTarihi);
                if ($bitisTarihi) {
                    $islemTurleri = $islemTurleri->where("$islemTabloAdi.created_at", '<=', $bitisTarihi);
                }
            } else if ($bitisTarihi) {
                $islemTurleri = $islemTurleri->where("$islemTabloAdi.created_at", '<=', $bitisTarihi);
            }

            $islemTurleri = $islemTurleri->get()->toArray();
            $hazirlananVeriler = [
                "veriler" => [],
                "chartVerileri" => [],
            ];

            foreach ($islemTurleri as &$islemTur) {
                $firinId = $islemTur["id"];
                $islemTur["json"] = json_decode($islemTur["json"], true);
                $islemTur["tekrarEtmeyenSayisi"] = $islemTur["toplam"] - $islemTur["toplamTekrarEden"];
                if (!isset ($hazirlananVeriler["veriler"][$firinId])) {
                    $hazirlananVeriler["veriler"][$firinId] = [
                        "id" => $firinId,
                        "ad" => $islemTur["ad"],
                        "kod" => $islemTur["kod"],
                        "json" => $islemTur["json"],
                        "islemTurleri" => [],
                    ];
                }

                unset($islemTur["id"], $islemTur["ad"], $islemTur["kod"], $islemTur["json"]);
                $hazirlananVeriler["veriler"][$firinId]["islemTurleri"][] = $islemTur;

                // Chart verileri hazırlanıyor
                if (!isset ($hazirlananVeriler["chartVerileri"][$firinId])) {
                    $hazirlananVeriler["chartVerileri"][$firinId] = [
                        "firinId" => $firinId,
                        "ad" => $hazirlananVeriler["veriler"][$firinId]["ad"],
                        "kod" => $hazirlananVeriler["veriler"][$firinId]["kod"],
                        "json" => $hazirlananVeriler["veriler"][$firinId]["json"],
                        "islemler" => [],
                        "tekrarEtmeyenSayisi" => [],
                        "tekrarEdenSayisi" => [],
                    ];
                }

                $key = array_search($islemTur["islemTuruAdi"], $hazirlananVeriler["chartVerileri"][$firinId]["islemler"]);
                if ($key === false) {
                    $hazirlananVeriler["chartVerileri"][$firinId]["islemler"][] = $islemTur["islemTuruAdi"];
                    $hazirlananVeriler["chartVerileri"][$firinId]["tekrarEtmeyenSayisi"][] = $islemTur["tekrarEtmeyenSayisi"];
                    $hazirlananVeriler["chartVerileri"][$firinId]["tekrarEdenSayisi"][] = $islemTur["toplamTekrarEden"];
                } else {
                    $hazirlananVeriler["chartVerileri"][$firinId]["tekrarEtmeyenSayisi"][$key] += $islemTur["tekrarEtmeyenSayisi"];
                    $hazirlananVeriler["chartVerileri"][$firinId]["tekrarEdenSayisi"][$key] += $islemTur["toplamTekrarEden"];
                }
            }

            $hazirlananVeriler["veriler"] = array_values($hazirlananVeriler["veriler"]);
            $hazirlananVeriler["chartVerileri"] = array_values($hazirlananVeriler["chartVerileri"]);

            return response()->json([
                "durum" => true,
                "mesaj" => "Firin bazli bilgiler getirildi",
                "islemTurleri" => $hazirlananVeriler,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "durum" => false,
                "mesaj" => "Firin bazli bilgiler getirilemedi",
                "hata" => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "FBI_CATCH",
            ]);
        }
    }
}
