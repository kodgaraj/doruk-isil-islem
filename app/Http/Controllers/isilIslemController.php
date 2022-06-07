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

    public function formlar(Request $request)
    {
        try
        {
            $formTabloAdi = (new Formlar())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

            $formlar = Formlar::select(DB::raw("
                    $formTabloAdi.id as id,
                    $formTabloAdi.formAdi,
                    $formTabloAdi.takipNo,
                    $formTabloAdi.baslangicTarihi,
                    $formTabloAdi.bitisTarihi,
                    COUNT(IF($islemTabloAdi.deleted_at IS NULL, $islemTabloAdi.id, NULL)) as islemSayisi
                "))
                ->join($islemTabloAdi, $formTabloAdi . '.id', '=', $islemTabloAdi . '.formId')
                ->groupBy(
                    $formTabloAdi . '.id',
                    $formTabloAdi . '.formAdi',
                    $formTabloAdi . '.takipNo',
                    $formTabloAdi . '.baslangicTarihi',
                    $formTabloAdi . '.bitisTarihi'
                )
                ->paginate(10);

            return response()->json([
                'durum' => true,
                "mesaj" => "Formlar başarıyla getirildi.",
                'formlar' => $formlar,
            ], 200);
        }
        catch (\Exception $ex)
        {
            return response()->json([
                "durum" => false,
                "mesaj" => "Formlar getirilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }

    public function formDetay(Request $request)
    {
        try
        {
            $formId = $request->formId;

            $formTabloAdi = (new Formlar())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

            $formBilgileri = Formlar::find($formId);
            $secilenIslemler = Islemler::select(DB::raw("
                    $islemTabloAdi.*,
                    $islemDurumTabloAdi.ad as islemDurumAdi,
                    $islemDurumTabloAdi.kod as islemDurumKodu
                "))
                ->join($islemDurumTabloAdi, $islemDurumTabloAdi . '.id', '=', $islemTabloAdi . '.durumId')
                ->where("$islemTabloAdi.formId", $formId)
                ->get();

            return response()->json([
                "durum" => true,
                "mesaj" => "Form detayları başarıyla getirildi.",
                "secilenIslemler" => $secilenIslemler,
            ], 200);
        }
        catch (\Exception $ex)
        {
            return response()->json([
                "durum" => false,
                "mesaj" => "Form detayları getirilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
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
                $takipNumarasi = substr($form->takipNo, 3);
                $takipNo = "TKP" . sprintf("%02d", $takipNumarasi + 1);
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
                    $islemTabloAdi.firinId,
                    $islemTabloAdi.sarj,
                    $islemTabloAdi.islemTuruId,
                    $islemTabloAdi.durumId as islemDurumId,
                    $islemTabloAdi.adet,
                    $islemTabloAdi.miktar,
                    $islemTabloAdi.dara,
                    $islemTabloAdi.kalite,
                    $islemTabloAdi.istenilenSertlik,
                    $islemTabloAdi.sicaklik,
                    $islemTabloAdi.carbon,
                    $islemTabloAdi.beklenenSure,
                    $islemTabloAdi.cikisSertligi,
                    $islemTabloAdi.menevisSicakligi,
                    $islemTabloAdi.cikisSuresi,
                    $islemTabloAdi.sonSertlik,
                    $islemTabloAdi.tekrar,
                    $islemTabloAdi.aciklama as islemAciklama,
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
                ->orderBy($siparisTabloAdi . '.siparisNo', 'desc')
                ->orderBy($islemTabloAdi . '.created_at', 'desc')
                ->orderBy($firmaTabloAdi . '.firmaAdi', 'asc');

            if (isset($request->formId) && $request->formId)
            {
                $firmaGrupluIslemler = $firmaGrupluIslemler->orWhere($islemTabloAdi . '.formId', $request->formId);
            }

            $islemler = $firmaGrupluIslemler->paginate(50);

            // dd($islemler->toArray());
            $islemler = $islemler->toArray();

            $hazirlananVeriler = [];
            foreach($islemler["data"] as $islem)
            {
                if (!isset($hazirlananVeriler[$islem["firmaId"]]))
                {
                    $hazirlananVeriler[$islem["firmaId"]] = [
                        "firmaId" => $islem["firmaId"],
                        'firmaAdi' => $islem["firmaAdi"],
                        "sorumluKisi" => $islem["sorumluKisi"],
                        'islemler' => [],
                    ];
                }

                $terminDizisi = $this->terminHesapla($islem["siparisTarihi"], $islem["terminSuresi"] ?? 5);
                $islem["gecenSure"] = $terminDizisi["gecenSure"];
                $islem["gecenSureRenk"] = $terminDizisi["gecenSureRenk"];

                $islem["sarj"] = $islem["sarj"] ?? 1;
                $islem["firin"] = $islem["firin"] ?? null;

                $hazirlananVeriler[$islem["firmaId"]]['islemler'][] = $islem;
            }

            $islemler["data"] = array_values($hazirlananVeriler);

            return response()->json([
                'durum' => true,
                'mesaj' => 'İşlemler bulundu.',
                'firmaGrupluIslemler' => $islemler,
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

    public function formKaydet(Request $request)
    {
        try
        {
            $formBilgileri = $request->form;
            $secilenIslemler = $formBilgileri["secilenIslemler"];

            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

            DB::beginTransaction();

            if (isset($formBilgileri["id"]) && $formBilgileri["id"])
            {
                $form = Formlar::find($formBilgileri["id"]);
            }
            else
            {
                $form = new Formlar();
            }
            $form->takipNo = $formBilgileri["takipNo"];
            $form->formAdi = $formBilgileri["formAdi"];
            $form->baslangicTarihi = $formBilgileri["baslangicTarihi"];
            $form->aciklama = $formBilgileri["aciklama"] ?? null;

            if (!$form->save())
            {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Form kaydedilemedi.',
                    "hataKodu" => "F001",
                ], 500);
            }

            // işlemleri güncelleme
            foreach($secilenIslemler as $secilen)
            {
                $islem = Islemler::find($secilen["id"]);

                if (!$islem)
                {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem bulunamadı.',
                        "hataKodu" => "I001",
                    ], 500);
                }

                $islemBekliyorDurum = IslemDurumlari::where("kod", "ISLEM_BEKLIYOR")->first();

                if (!$islemBekliyorDurum)
                {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem durumu bulunamadı.',
                        "hataKodu" => "I002",
                    ], 500);
                }

                $islem->formId = $form->id;
                $islem->durumId = $islemBekliyorDurum->id;
                $islem->firinId = $secilen["firin"]["id"];
                $islem->sarj = $secilen["sarj"];
                $islem->kalite = $secilen["kalite"];
                $islem->istenilenSertlik = $secilen["istenilenSertlik"];
                $islem->sicaklik = $secilen["sicaklik"] ?? null;
                $islem->carbon = $secilen["carbon"] ?? null;
                $islem->beklenenSure = $secilen["beklenenSure"] ?? null;
                $islem->cikisSertligi = $secilen["cikisSertligi"] ?? null;
                $islem->menevisSicakligi = $secilen["menevisSicakligi"] ?? null;
                $islem->cikisSuresi = $secilen["cikisSuresi"] ?? null;
                $islem->sonSertlik = $secilen["sonSertlik"] ?? null;
                $islem->tekrar = $secilen["tekrar"] ?? 0;
                $islem->aciklama = $secilen["aciklama"] ?? null;

                if (!$islem->save())
                {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem kaydedilemedi.',
                        "hataKodu" => "I003",
                    ], 500);
                }
            }

            if (isset($formBilgileri["silinecekIslemler"]) && $formBilgileri["silinecekIslemler"])
            {
                foreach ($formBilgileri["silinecekIslemler"] as $islemId)
                {
                    $islem = Islemler::find($islemId);

                    if (!$islem)
                    {
                        DB::rollBack();

                        return response()->json([
                            'durum' => false,
                            'mesaj' => 'İşlem bulunamadı.',
                            "hataKodu" => "I004",
                        ], 500);
                    }

                    $islemDurumu = IslemDurumlari::where("kod", "BASLANMADI")->first();

                    $islem->formId = null;
                    $islem->firinId = null;
                    $islem->sarj = null;
                    $islem->durumId = $islemDurumu->id;

                    if (!$islem->save())
                    {
                        DB::rollBack();

                        return response()->json([
                            'durum' => false,
                            'mesaj' => 'İşlem kaydedilemedi.',
                            "hataKodu" => "I006",
                        ], 500);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Form kaydedildi.',
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

    public function formSil(Request $request)
    {
        try
        {
            $form = Formlar::find($request->formId);

            if (!$form)
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Form bulunamadı.',
                    "hataKodu" => "F002",
                ], 500);
            }

            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

            DB::beginTransaction();

            $baslanmisIslemSayisi = Islemler::join($islemDurumTabloAdi, $islemDurumTabloAdi . ".id", "=", $islemTabloAdi . ".durumId")
                ->where("formId", $form->id)
                ->whereNotIn($islemDurumTabloAdi . ".kod", ["BASLANMADI", "ISLEM_BEKLIYOR"])
                ->count();

            if ($baslanmisIslemSayisi > 0)
            {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Form içerisinde işlemlerden bazıları ısıl işlem gördüğünden silinemez.',
                    "hataKodu" => "F003",
                ], 500);
            }

            $baslanmadiDurum = IslemDurumlari::where("kod", "BASLANMADI")->first();

            $updateIslemler = Islemler::where("formId", $form->id)->get();

            foreach ($updateIslemler as $islem)
            {
                $islem->formId = null;
                $islem->firinId = null;
                $islem->sarj = null;
                $islem->durumId = $baslanmadiDurum->id;

                if (!$islem->save())
                {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem kaydedilemedi.',
                        "hataKodu" => "F004",
                    ], 500);
                }
            }

            if (!$form->delete())
            {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Form silinemedi.',
                    "hataKodu" => "F004",
                ], 500);
            }

            DB::commit();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Form silindi.',
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
