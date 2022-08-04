<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExporter;
use App\Models\Firmalar;
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
        ]);
    }

    public function siparisler(Request $request)
    {
        try
        {
            $filtrelemeler = json_decode($request->filtreleme ?? "[]", true);

            $cikti = isset($request->cikti) && json_decode($request->cikti) === true;

            $sayfalamaSayisi = $cikti ? 9999 : ($request->sayfalamaSayisi ?? 10);
            $firmaTabloAdi = (new Firmalar())->getTable();
            $siparisDurumTabloAdi = (new SiparisDurumlari())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();
            $kullaniciTabloAdi = (new User())->getTable();

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
                    $siparisTabloAdi.aciklama,
                    $siparisDurumTabloAdi.ad as siparisDurumAdi,
                    $firmaTabloAdi.firmaAdi,
                    $firmaTabloAdi.sorumluKisi,
                    $kullaniciTabloAdi.name as duzenleyen,
                    COUNT(IF($islemTabloAdi.deleted_at IS NULL, $islemTabloAdi.id, NULL)) as islemSayisi,
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
                    ) as tutarUSD
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
                    $siparisTabloAdi . '.aciklama',
                    $siparisDurumTabloAdi . '.ad',
                    $firmaTabloAdi . '.firmaAdi',
                    $firmaTabloAdi . '.sorumluKisi',
                    $kullaniciTabloAdi . '.name',
                )
                ->orderBy($siparisTabloAdi . '.created_at', 'desc');

            if (isset($filtrelemeler["termin"]) && $filtrelemeler["termin"] > 0)
            {
                $tarih = Carbon::now()->subDays($filtrelemeler["termin"])->format('Y-m-d');

                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", "<=", $tarih);
            }

            if (isset($filtrelemeler["arama"]) && $filtrelemeler["arama"] != "")
            {
                // Sipariş no, firma adı, irsaliye no
                $siparisler = $siparisler->where(function ($query) use ($filtrelemeler, $siparisTabloAdi, $firmaTabloAdi) {
                    $query->where($siparisTabloAdi . '.siparisNo', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($firmaTabloAdi . '.firmaAdi', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($siparisTabloAdi . '.irsaliyeNo', 'like', '%' . $filtrelemeler["arama"] . '%');
                });
            }

            if (isset($filtrelemeler["firma"]) && $filtrelemeler["firma"] && count($filtrelemeler["firma"]) > 0)
            {
                $firmaIdleri = array_column($filtrelemeler["firma"], "id");

                $siparisler = $siparisler->whereIn("$firmaTabloAdi.id", $firmaIdleri);
            }

            if (isset($filtrelemeler["baslangicTarihi"]) && $filtrelemeler["baslangicTarihi"] != "")
            {
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", ">=", $filtrelemeler["baslangicTarihi"]);
            }

            if (isset($filtrelemeler["bitisTarihi"]) && $filtrelemeler["bitisTarihi"] != "")
            {
                $siparisler = $siparisler->where("$siparisTabloAdi.tarih", "<=", $filtrelemeler["bitisTarihi"]);
            }

            if (isset($filtrelemeler["siparisId"]) && $filtrelemeler["siparisId"])
            {
                $siparisler = $siparisler->where("$siparisTabloAdi.id", $filtrelemeler["siparisId"]);
            }

            $siparisler = $siparisler->paginate($sayfalamaSayisi)->toArray();

            foreach ($siparisler["data"] as &$siparis)
            {
                $terminBilgileri = $this->terminHesapla($siparis["tarih"], $siparis["terminSuresi"]);

                $siparis["gecenSure"] = $terminBilgileri["gecenSure"];
                $siparis["gecenSureRenk"] = $terminBilgileri["gecenSureRenk"];

                $siparis["tutarTLYazi"] = $this->yaziyaDonustur($siparis["tutarTL"], [
                    "paraBirimi" => $this->paraBirimleri["TL"],
                ]);
                $siparis["tutarUSDYazi"] = $this->yaziyaDonustur($siparis["tutarUSD"], [
                    "paraBirimi" => $this->paraBirimleri["USD"],
                ]);
                $siparis["netYazi"] = $this->yaziyaDonustur($siparis["net"], ["kg" => true]);
            }

            if ($cikti)
            {
                return (
                    new ExcelExporter($siparisler["data"], [
                        "siparisId" => "Sipariş ID",
                        "siparisNo" => "Sipariş No",
                        "gecenSure" => "Termin",
                        "firmaAdi" => "Firma",
                        "islemSayisi" => "İşlem Sayısı",
                        "netYazi" => "Miktar (Net)",
                        "tutarTLYazi" => "Tutar (TL)",
                        "tutarUSDYazi" => "Tutar (USD)",
                        [
                            "key" => "tarih",
                            "value" => "Sipariş Tarihi",
                            "tur" => "TARIH"
                        ],
                    ])
                )->downloadExcel("Sipariş Listesi");
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Siparişler başarıyla getirildi.',
                'siparisler' => $siparisler
            ]);
        }
        catch(\Exception $e)
        {
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
        try
        {
            $siparisNo = Siparisler::max('siparisNo');

            if(!$siparisNo)
            {
                $siparisNo = 'DRK0000001';
            }
            else
            {
                $siparisNo = substr($siparisNo, 3);
                $siparisNo = 'DRK' . sprintf('%07d', $siparisNo + 1);
            }

            $irsaliyeNo = Siparisler::max('irsaliyeNo');

            if(!$irsaliyeNo)
            {
                $irsaliyeNo = 'IR0000001';
            }
            else
            {
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
        }
        catch(\Exception $e)
        {
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
        try
        {
            $siparisDurumlari = SiparisDurumlari::all();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş durumları başarıyla getirildi.',
                'siparisDurumlari' => $siparisDurumlari
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }

    public function siparisKaydet(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $siparisBilgileri = $request->siparis;
            $userId = Auth::user()->id;
            // dd($siparisBilgileri);

            if (isset($siparisBilgileri['siparisId']))
            {
                $siparis = Siparisler::find($siparisBilgileri['siparisId']);

                if ($siparis->siparisNo != $siparisBilgileri['siparisNo'] && Siparisler::where('siparisNo', $siparisBilgileri['siparisNo'])->count() > 0)
                {
                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'Bu sipariş numarası zaten kullanılıyor.',
                        "hataKodu" => "SK001"
                    ]);
                }
            }
            else
            {
                $siparis = new Siparisler();

                if (Siparisler::where('siparisNo', $siparisBilgileri['siparisNo'])->count() > 0)
                {
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

            if (!$siparis->save())
            {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Sipariş kaydedilirken bir hata oluştu.',
                    'hata' => $siparis->getErrors(),
                    "hataKodu" => "S001"
                ], 500);
            }

            foreach ($siparisBilgileri['islemler'] as $key => $islem)
            {
                if (isset($islem['id']))
                {
                    $islemModel = Islemler::find($islem['id']);
                }
                else
                {
                    $islemModel = new Islemler();
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
                $islemModel->json = $islem['json'] ?? null;

                if (isset($islem["yeniResimSecildi"], $islem["resim"]) && $islem["yeniResimSecildi"] && $islem["resim"])
                {
                    if (isset($islem["resimYolu"]) && $islem["resimYolu"])
                    {
                        $this->dosyaSil($islem["resimYolu"]);
                    }

                    $resimYolu = $this->base64ResimKaydet($islem["resim"], [
                        "dosyaAdi" => "$siparis->siparisNo-$islemModel->siraNo"
                    ]);

                    if (!$resimYolu)
                    {
                        DB::rollBack();

                        return response()->json([
                            'durum' => false,
                            'mesaj' => 'Resim kaydedilirken bir hata oluştu.',
                            "hataKodu" => "S003"
                        ], 500);
                    }

                    $islemModel->resimYolu = $resimYolu;
                }

                if (!$islemModel->save())
                {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem kaydedilirken bir hata oluştu.',
                        'hata' => $islemModel->getErrors(),
                        "hataKodu" => "S002"
                    ], 500);
                }
            }

            if (isset($siparisBilgileri['silinenIslemler']) && $siparisBilgileri['silinenIslemler'])
            {
                foreach ($siparisBilgileri['silinenIslemler'] as $islemId)
                {
                    $islemModel = Islemler::where("id", $islemId)->first();

                    // Eğer resimYolu varsa sil
                    if ($islemModel->resimYolu)
                    {
                        $resimSilmeDurum = $this->dosyaSil($islemModel->resimYolu);

                        if (!$resimSilmeDurum)
                        {
                            DB::rollBack();

                            return response()->json([
                                'durum' => false,
                                'mesaj' => 'Resim silinirken bir hata oluştu.',
                                "hataKodu" => "S004"
                            ], 500);
                        }
                    }

                    if (!$islemModel->delete())
                    {
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
        }
        catch(\Exception $e)
        {
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
        try
        {
            $siparisId = $request->siparisId;
            $siparisDetaylariGetir = $request->detaylariGetir;

            $islemler = Islemler::where('siparisId', $siparisId)->get();

            $donecekVeriler = [
                "islemler" => $islemler,
            ];

            if ($siparisDetaylariGetir)
            {
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

            foreach ($donecekVeriler["islemler"] as &$islem)
            {
                $paraBirimi = $this->paraBirimleri[$islem["paraBirimi"]];
                $islem["miktarYazi"] = $this->yaziyaDonustur($islem["miktar"], ["kg" => true]);
                $islem["daraYazi"] = $this->yaziyaDonustur($islem["dara"], ["kg" => true]);
                $islem["birimFiyatYazi"] = $this->yaziyaDonustur($islem["birimFiyat"], [
                    "paraBirimi" => $paraBirimi
                ]);
                $islem["paraBirimi"] = $paraBirimi;
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş başarıyla getirildi.',
                'veriler' => $donecekVeriler,
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }

    public function siparisSil(Request $request)
    {
        DB::beginTransaction();
        try
        {
            $siparisId = $request->siparisId;

            $islemler = Islemler::where('siparisId', $siparisId)->get();

            foreach ($islemler as $islem)
            {
                // Eğer resimYolu varsa sil
                if ($islem->resimYolu)
                {
                    $resimSilmeDurum = $this->dosyaSil($islem->resimYolu);

                    if (!$resimSilmeDurum)
                    {
                        DB::rollBack();

                        return response()->json([
                            'durum' => false,
                            'mesaj' => 'Resim silinirken bir hata oluştu.',
                            "hataKodu" => "S008"
                        ], 500);
                    }
                }

                if (!$islem->delete())
                {
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

            if (!$siparis)
            {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Sipariş bulunamadı.',
                    "hataKodu" => "S011"
                ], 404);
            }

            if (!$siparis->delete())
            {
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
        }
        catch(\Exception $e)
        {
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
        try
        {
            $toplamSiparis = Siparisler::count();

            return response()->json([
                "durum" => true,
                "mesaj" => "Toplam sipariş sayısı bulundu.",
                "toplamSiparis" => $toplamSiparis,
            ], 200);
        }
        catch (\Exception $ex)
        {
            return response()->json([
                "durum" => false,
                "mesaj" => "Toplam sipariş sayısı bulunurken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }
}
