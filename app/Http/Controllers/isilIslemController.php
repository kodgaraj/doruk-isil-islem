<?php

namespace App\Http\Controllers;

use App\Models\Firmalar;
use App\Models\Formlar;
use App\Models\IslemDurumlari;
use App\Models\Islemler;
use App\Models\IslemTurleri;
use App\Models\Malzemeler;
use App\Models\Siparisler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IsilIslemController extends Controller
{
    public function index()
    {
        return view('isil-islemler');
    }

    /**
     * Tarih ile başlayan takip numarasının bir sonraki numarasını döndürür.
     * Örn: TKP2022060301 -> TKP2022060302
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function takipNumarasiGetir()
    {
        try
        {
            $takipNo = "TKP" . date("Ymd");
            $form = Formlar::where('takipNo', 'like', "$takipNo%")
                ->orderBy('takipNo', 'desc')
                ->first();

            if(!$form)
            {
                $takipNo .= '01';
            }
            else
            {
                $takipNumarasi = substr($form->takip_numarasi, 3);
                $takipNumarasi = $takipNo . sprintf("%02d", $takipNumarasi + 1);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Takip numarası bulundu.',
                'takipNo' => $takipNo,
            ], 200);
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

    /**
     * Firma gruplu işlemleri sayfalayarak getirir
     */
    public function firmaGrupluIslemleriGetir(Request $request)
    {
        try
        {
            $siparisTabloAdi = (new Siparisler())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();
            $firmaTabloAdi = (new Firmalar())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $malzemeTabloAdi = (new Malzemeler())->getTable();
            $islemTuruTabloAdi = (new IslemTurleri())->getTable();

            $firmaGrupluIslemler = Islemler::select(DB::raw("
                    $islemTabloAdi.id,
                    $islemTabloAdi.siparisId,
                    $islemTabloAdi.malzemeId,
                    $islemTabloAdi.islemTuruId,
                    $islemTabloAdi.durumId as islemDurumId,
                    $islemTabloAdi.adet,
                    $islemTabloAdi.miktar,
                    $islemTabloAdi.dara,
                    $islemTabloAdi.kalite,
                    $islemTabloAdi.istenilenSertlik,
                    $siparisTabloAdi.firmaId,
                    $siparisTabloAdi.durumId as siparisDurumId,
                    $siparisTabloAdi.ad as siparisAdi,
                    $siparisTabloAdi.siparisNo,
                    $siparisTabloAdi.aciklama,
                    $siparisTabloAdi.terminSuresi,
                    $siparisTabloAdi.tarih as siparisTarihi,
                    $firmaTabloAdi.firmaAdi,
                    $firmaTabloAdi.sorumluKisi,
                    $islemDurumTabloAdi.ad as islemDurumAdi,
                    $malzemeTabloAdi.ad as malzemeAdi,
                    $islemTuruTabloAdi.ad as islemTuruAdi
                "))
                ->join($siparisTabloAdi, $siparisTabloAdi . '.id', '=', $islemTabloAdi . '.siparisId')
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($islemDurumTabloAdi, $islemDurumTabloAdi . '.id', '=', $islemTabloAdi . '.durumId')
                ->join($malzemeTabloAdi, $malzemeTabloAdi . '.id', '=', $islemTabloAdi . '.malzemeId')
                ->leftJoin($islemTuruTabloAdi, $islemTuruTabloAdi . '.id', '=', $islemTabloAdi . '.islemTuruId')
                ->where($islemDurumTabloAdi . '.kod', "BASLANMADI")
                ->where($islemTabloAdi . '.formId', null)
                ->orderBy($siparisTabloAdi . '.siparisNo', 'desc')
                ->orderBy($islemTabloAdi . '.created_at', 'desc')
                ->orderBy($firmaTabloAdi . '.firmaAdi', 'asc')
                ->paginate(50);

            // dd($firmaGrupluIslemler->toArray());

            $hazirlananVeriler = [];
            foreach($firmaGrupluIslemler as $islem)
            {
                if (!isset($hazirlananVeriler[$islem->firmaId]))
                {
                    $hazirlananVeriler[$islem->firmaId] = [
                        "firmaId" => $islem->firmaId,
                        'firmaAdi' => $islem->firmaAdi,
                        "sorumluKisi" => $islem->sorumluKisi,
                        'islemler' => [],
                    ];
                }

                $terminDizisi = $this->terminHesapla($islem->siparisTarihi, $islem->terminSuresi ?? 5);
                $islem->gecenSure = $terminDizisi["gecenSure"];
                $islem->gecenSureRenk = $terminDizisi["gecenSureRenk"];

                $islem->sarj = 1;
                $islem->firin = null;

                $hazirlananVeriler[$islem->firmaId]['islemler'][] = $islem;
            }

            $firmaGrupluIslemler = array_values($hazirlananVeriler);

            return response()->json([
                'durum' => true,
                'mesaj' => 'İşlemler bulundu.',
                'firmaGrupluIslemler' => $firmaGrupluIslemler,
            ], 200);
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
}
