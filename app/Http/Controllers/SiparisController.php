<?php

namespace App\Http\Controllers;

use App\Models\Firmalar;
use App\Models\Islemler;
use App\Models\IslemTurleri;
use App\Models\Malzemeler;
use App\Models\SiparisDurumlari;
use App\Models\Siparisler;
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
        return view('siparis-formu');
    }

    public function siparisler(Request $request)
    {
        try
        {
            $sayfalamaSayisi = $request->sayfalamaSayisi ?? 10;
            $firmaTabloAdi = (new Firmalar())->getTable();
            $siparisDurumTabloAdi = (new SiparisDurumlari())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

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
                    COUNT(IF($islemTabloAdi.deleted_at IS NULL, $islemTabloAdi.id, NULL)) as islemSayisi
                "))
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($siparisDurumTabloAdi, $siparisDurumTabloAdi . '.id', '=', $siparisTabloAdi . '.durumId')
                ->leftJoin($islemTabloAdi, $islemTabloAdi . '.siparisId', '=', $siparisTabloAdi . '.id')
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
                    $firmaTabloAdi . '.sorumluKisi'
                )
                ->paginate($sayfalamaSayisi);

                // dd($siparisler->toSql());

            // dd($siparisler->getPageName());

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
     * Örnek: SPR000001 -> SPR000002
     */
    public function numaralariGetir(Request $request)
    {
        try
        {
            $siparisNo = Siparisler::max('siparisNo');

            if(!$siparisNo)
            {
                $siparisNo = 'SPR0000001';
            }
            else
            {
                $siparisNo = substr($siparisNo, 3);
                $siparisNo = 'SPR' . sprintf('%07d', $siparisNo + 1);
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

    /**
     * @global
     */
    public function firmalariGetir()
    {
        try
        {
            $firmalar = Firmalar::all();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Firmalar başarıyla getirildi.',
                'firmalar' => $firmalar
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
        try
        {
            $siparisBilgileri = $request->siparis;
            $userId = Auth::user()->id;
            // dd($siparisBilgileri);

            DB::beginTransaction();

            if (isset($siparisBilgileri['siparisId']))
            {
                $siparis = Siparisler::find($siparisBilgileri['siparisId']);
            }
            else
            {
                $siparis = new Siparisler();
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
                $islemModel->adet = $islem['adet'];
                $islemModel->miktar = $islem['miktar'];
                $islemModel->dara = $islem['dara'];
                $islemModel->birimFiyat = $islem['birimFiyat'];
                $islemModel->kalite = $islem['kalite'];
                $islemModel->istenilenSertlik = $islem['istenilenSertlik'];
                $islemModel->json = $islem['json'] ?? null;

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

                    if (!$islemModel->delete())
                    {
                        DB::rollBack();

                        return response()->json([
                            'durum' => false,
                            'mesaj' => 'İşlem silinirken bir hata oluştu.',
                            'hata' => $islemModel->getErrors(),
                            "hataKodu" => "S003"
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

            $malzemeTabloAdi = (new Malzemeler())->getTable();
            $islemTuruTabloAdi = (new IslemTurleri())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

            $islemler = Islemler::where('siparisId', $siparisId)->get();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş başarıyla getirildi.',
                'veriler' => [
                    "islemler" => $islemler,
                ],
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
        try
        {
            $siparisId = $request->siparisId;

            DB::beginTransaction();

            $islemler = Islemler::where('siparisId', $siparisId)->get();

            foreach ($islemler as $islem)
            {
                if (!$islem->delete())
                {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem silinirken bir hata oluştu.',
                        'hata' => $islem->getErrors(),
                        "hataKodu" => "S004"
                    ], 500);
                }
            }

            $siparis = Siparisler::find($siparisId);

            if (!$siparis)
            {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Sipariş bulunamadı.'
                ], 404);
            }

            if (!$siparis->delete())
            {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Sipariş silinirken bir hata oluştu.'
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
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }
}
