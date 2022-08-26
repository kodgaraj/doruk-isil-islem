<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExporter;
use App\Models\Firinlar;
use App\Models\Firmalar;
use App\Models\Formlar;
use App\Models\IslemDurumlari;
use App\Models\Islemler;
use App\Models\IslemTurleri;
use App\Models\Malzemeler;
use App\Models\SiparisDurumlari;
use App\Models\Siparisler;
use App\Models\User;
use Carbon\Carbon;
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
            $filtrelemeler = json_decode($request->filtreleme ?? "[]", true);

            $sayfalamaSayisi = $request->sayfalamaSayisi ?? 10;
            $formTabloAdi = (new Formlar())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();
            $kullaniciTabloAdi = (new User())->getTable();

            $formlar = Formlar::select(DB::raw("
                    $formTabloAdi.id as id,
                    $formTabloAdi.formAdi,
                    $formTabloAdi.takipNo,
                    $formTabloAdi.baslangicTarihi,
                    $formTabloAdi.bitisTarihi,
                    $kullaniciTabloAdi.name as duzenleyen,
                    COUNT(IF($islemTabloAdi.deleted_at IS NULL, $islemTabloAdi.id, NULL)) as islemSayisi
                "))
                ->join($islemTabloAdi, $formTabloAdi . '.id', '=', $islemTabloAdi . '.formId')
                ->leftJoin($kullaniciTabloAdi, $formTabloAdi . '.userId', '=', $kullaniciTabloAdi . '.id')
                ->groupBy(
                    $formTabloAdi . '.id',
                    $formTabloAdi . '.formAdi',
                    $formTabloAdi . '.takipNo',
                    $formTabloAdi . '.baslangicTarihi',
                    $formTabloAdi . '.bitisTarihi',
                    $kullaniciTabloAdi . '.name'
                )
                ->orderBy($formTabloAdi . '.id', 'desc');

            if (isset($filtrelemeler["formId"]) && $filtrelemeler["formId"])
            {
                $formlar = $formlar->where($formTabloAdi . '.id', $filtrelemeler["formId"]);
            }

            if (isset($filtrelemeler["arama"]) && $filtrelemeler["arama"] != "") {
                // Sipariş no, firma adı, irsaliye no
                $formlar = $formlar->where(function ($query) use ($filtrelemeler, $formTabloAdi) {
                    $query->where($formTabloAdi . '.takipNo', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($formTabloAdi . '.formAdi', 'like', '%' . $filtrelemeler["arama"] . '%');                                            
                });
            }

            if (isset($filtrelemeler["baslangicTarihi"]) && $filtrelemeler["baslangicTarihi"] != "") {
                $formlar = $formlar->where("$formTabloAdi.baslangicTarihi", ">=", $filtrelemeler["baslangicTarihi"]);
            }

            if (isset($filtrelemeler["bitisTarihi"]) && $filtrelemeler["bitisTarihi"] != "") {
                $formlar = $formlar->where("$formTabloAdi.baslangicTarihi", "<=", $filtrelemeler["bitisTarihi"]);
            }

            $formlar = $formlar->paginate($sayfalamaSayisi)->toArray();


            return response()->json([
                'durum' => true,
                "mesaj" => "Formlar başarıyla getirildi.",
                'formlar' => $formlar,
            ], 200);
        } catch (\Exception $ex) {
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
        try {
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
        } catch (\Exception $ex) {
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
        try {
            $takipNo = "TKP" . date("Ymd");
            $form = Formlar::where('takipNo', 'like', "$takipNo%")
                ->orderBy('takipNo', 'desc')
                ->first();

            if (!$form) {
                $takipNo .= '01';
            } else {
                $takipNumarasi = substr($form->takipNo, 3);
                $takipNo = "TKP" . sprintf("%02d", $takipNumarasi + 1);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Takip numarası bulundu.',
                'takipNo' => $takipNo,
            ], 200);
        } catch (\Exception $e) {
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
            $formId = $request->formId ?? null;
            $firmaId = $request->firmaId ?? null;
            $filtrelemeler = json_decode($request->filtreleme ?? "[]", true);

            $siparisTabloAdi = (new Siparisler())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();
            $firmaTabloAdi = (new Firmalar())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $malzemeTabloAdi = (new Malzemeler())->getTable();
            $islemTuruTabloAdi = (new IslemTurleri())->getTable();
            $firinTabloAdi = (new Firinlar())->getTable();

            $firmaGrupluIslemler = Islemler::select(DB::raw("
                    $islemTabloAdi.id,
                    $islemTabloAdi.siparisId,
                    $islemTabloAdi.malzemeId,
                    $islemTabloAdi.firinId,
                    $islemTabloAdi.sarj,
                    $islemTabloAdi.islemTuruId,
                    $islemTabloAdi.durumId as islemDurumId,
                    $islemTabloAdi.bolunmusId,
                    $islemTabloAdi.adet,
                    $islemTabloAdi.miktar,
                    $islemTabloAdi.dara,
                    ($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                    $islemTabloAdi.kalite,
                    $islemTabloAdi.istenilenSertlik,
                    $islemTabloAdi.sicaklik,
                    $islemTabloAdi.carbon,
                    $islemTabloAdi.beklenenSure,
                    $islemTabloAdi.cikisSertligi,
                    $islemTabloAdi.menevisSicakligi,
                    $islemTabloAdi.cikisSuresi,
                    $islemTabloAdi.sonSertlik,
                    $islemTabloAdi.tekrarEdenId,
                    $islemTabloAdi.tekrarEdilenId,
                    $islemTabloAdi.resimYolu,
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
                    $islemTuruTabloAdi.ad as islemTuruAdi,
                    $firinTabloAdi.ad as firinAdi,
                    $firinTabloAdi.kod as firinKodu,
                    $firinTabloAdi.json as firinJson
                "))
                ->join($siparisTabloAdi, $siparisTabloAdi . '.id', '=', $islemTabloAdi . '.siparisId')
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($islemDurumTabloAdi, $islemDurumTabloAdi . '.id', '=', $islemTabloAdi . '.durumId')
                ->join($malzemeTabloAdi, $malzemeTabloAdi . '.id', '=', $islemTabloAdi . '.malzemeId')
                ->leftJoin($islemTuruTabloAdi, $islemTuruTabloAdi . '.id', '=', $islemTabloAdi . '.islemTuruId')
                ->leftJoin($firinTabloAdi, $firinTabloAdi . '.id', '=', $islemTabloAdi . '.firinId');

            /**
             * DİKKAT!!!
             * 
             * Aşağıdaki satırların sıralamasını değiştirmeyin.
             * Sebep; Laravel query builder sapıtıyor veya ben
             * gözümden bir şey kaçırıyorum.
             */
            if ($formId)
            {
                $secilenIslemler = clone $firmaGrupluIslemler;
                $secilenIslemler = $secilenIslemler->where("$islemTabloAdi.formId", $formId)
                    ->get()
                    ->toArray();
            }

            $firmaGrupluIslemler = $firmaGrupluIslemler->where($islemDurumTabloAdi . '.kod', "BASLANMADI");

            if ($formId)
            {
                $firmaGrupluIslemler = $firmaGrupluIslemler->orWhere($islemTabloAdi . '.formId', $formId)
                    ->selectRaw("IF($islemTabloAdi.formId = $formId, 1, 0) as secildi")
                    ->orderBy('secildi', 'desc');
            }

            $firmaGrupluIslemler = $firmaGrupluIslemler->orderBy($firmaTabloAdi . '.firmaAdi', 'asc')
                ->orderBy($siparisTabloAdi . '.siparisNo', 'desc')
                ->orderBy($islemTabloAdi . '.created_at', 'desc');
            /** DİKKAT SON!!! */

            if ($firmaId)
            {
                $firmaGrupluIslemler = $firmaGrupluIslemler->where($siparisTabloAdi . '.firmaId', $firmaId);
            }

            if (isset($filtrelemeler["arama"]) && $filtrelemeler["arama"] != "")
            {
                // dd($filtrelemeler["arama"]);
                $firmaGrupluIslemler = $firmaGrupluIslemler->where(function ($query) use ($filtrelemeler, $firmaTabloAdi, $malzemeTabloAdi, $islemTuruTabloAdi) {
                    $query->orWhere($firmaTabloAdi . '.firmaAdi', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($firmaTabloAdi . '.sorumluKisi', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($malzemeTabloAdi . '.ad', 'like', '%' . $filtrelemeler["arama"] . '%')
                        ->orWhere($islemTuruTabloAdi . '.ad', 'like', '%' . $filtrelemeler["arama"] . '%');
                });
            }

            $islemler = $firmaGrupluIslemler->paginate(10);

            // dd($islemler->toArray());
            $islemler = $islemler->toArray();

            foreach ($islemler["data"] as &$islem)
            {
                $terminDizisi = $this->terminHesapla($islem["siparisTarihi"], $islem["terminSuresi"] ?? 5);
                $islem["gecenSure"] = $terminDizisi["gecenSure"];
                $islem["gecenSureRenk"] = $terminDizisi["gecenSureRenk"];
                $islem["net"] = (float) $islem["net"];

                $islem["sarj"] = $islem["sarj"] ?? 1;
                $islem["firin"] = $islem["firinId"]
                    ? [
                        "id" => $islem["firinId"],
                        "ad" => $islem["firinAdi"],
                        "kod" => $islem["firinKodu"],
                        "json" => $islem["firinJson"] ? json_decode($islem["firinJson"], true) : null,
                    ]
                    : null;
            }

            if ($formId)
            {
                foreach ($secilenIslemler as &$islem)
                {
                    $islem["firin"] = [
                        "id" => $islem["firinId"],
                        "ad" => $islem["firinAdi"],
                        "kod" => $islem["firinKodu"],
                        "json" => $islem["firinJson"] ? json_decode($islem["firinJson"], true) : null,
                    ];
                }
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'İşlemler bulundu.',
                'firmaGrupluIslemler' => $islemler,
                "secilenIslemler" => $secilenIslemler ?? null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                'satir' => $e->getLine(),
            ], 500);
        }
    }

    public function formKaydet(Request $request)
    {
        DB::beginTransaction();

        try {
            $formBilgileri = $request->form;
            $secilenIslemler = $formBilgileri["secilenIslemler"];

            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

            if (isset($formBilgileri["id"]) && $formBilgileri["id"]) {
                $form = Formlar::find($formBilgileri["id"]);
            } else {
                $form = new Formlar();
            }

            $form->userId = auth()->user()->id;
            $form->takipNo = $formBilgileri["takipNo"];
            $form->formAdi = $formBilgileri["formAdi"];
            $form->baslangicTarihi = $formBilgileri["baslangicTarihi"];
            $form->aciklama = $formBilgileri["aciklama"] ?? null;

            if (!$form->save()) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Form kaydedilemedi.',
                    "hataKodu" => "F001",
                ], 500);
            }

            // işlemleri güncelleme
            $islemBekliyorDurum = IslemDurumlari::where("kod", "ISLEM_BEKLIYOR")->first();

            if (!$islemBekliyorDurum) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem durumu bulunamadı.',
                    "hataKodu" => "I002",
                ], 500);
            }

            $islemBaslanmadiDurum = IslemDurumlari::where("kod", "BASLANMADI")->first();

            if (!$islemBaslanmadiDurum) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem durumu bulunamadı.',
                    "hataKodu" => "I003",
                ], 500);
            }

            foreach ($secilenIslemler as $secilen) {
                $islem = Islemler::find($secilen["id"]);

                if (!$islem) {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem bulunamadı.',
                        "hataKodu" => "I001",
                    ], 500);
                }

                $islem->formId = $form->id;
                $islem->durumId = $islem->durumId === $islemBaslanmadiDurum->id
                    ? $islemBekliyorDurum->id
                    : $islem->durumId;
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
                $islem->tekrarEdenId = $secilen["tekrarEdenId"] ?? null;
                $islem->tekrarEdilenId = $secilen["tekrarEdilenId"] ?? null;
                $islem->aciklama = $secilen["aciklama"] ?? null;

                if (!$islem->save()) {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem kaydedilemedi.',
                        "hataKodu" => "I003",
                    ], 500);
                }
            }

            if (isset($formBilgileri["silinecekIslemler"]) && $formBilgileri["silinecekIslemler"]) {
                foreach ($formBilgileri["silinecekIslemler"] as $islemId) {
                    $islem = Islemler::find($islemId);

                    if (!$islem) {
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

                    if (!$islem->save()) {
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
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                'satir' => $e->getLine(),
            ], 500);
        }
    }

    public function formSil(Request $request)
    {
        DB::beginTransaction();

        try {
            $form = Formlar::find($request->formId);

            if (!$form) {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Form bulunamadı.',
                    "hataKodu" => "F002",
                ], 500);
            }

            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

            $baslanmisIslemSayisi = Islemler::join($islemDurumTabloAdi, $islemDurumTabloAdi . ".id", "=", $islemTabloAdi . ".durumId")
                ->where("formId", $form->id)
                ->whereNotIn($islemDurumTabloAdi . ".kod", ["BASLANMADI", "ISLEM_BEKLIYOR"])
                ->count();

            if ($baslanmisIslemSayisi > 0) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Form içerisinde işlemlerden bazıları ısıl işlem gördüğünden silinemez.',
                    "hataKodu" => "F003",
                ], 500);
            }

            $baslanmadiDurum = IslemDurumlari::where("kod", "BASLANMADI")->first();

            $updateIslemler = Islemler::where("formId", $form->id)->get();

            foreach ($updateIslemler as $islem) {
                $islem->formId = null;
                $islem->firinId = null;
                $islem->sarj = null;
                $islem->durumId = $baslanmadiDurum->id;

                if (!$islem->save()) {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'İşlem kaydedilemedi.',
                        "hataKodu" => "F004",
                    ], 500);
                }
            }

            if (!$form->delete()) {
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
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                'satir' => $e->getLine(),
            ], 500);
        }
    }

    public function islemler(Request $request)
    {
        try
        {
            $filtrelemeler = json_decode($request->filtreleme ?? "[]", true);

            $cikti = isset($request->cikti) && json_decode($request->cikti) === true;

            $sayfalamaSayisi = $cikti ? 9999 : ($filtrelemeler["limit"] ?? 6);

            $islemTabloAdi = (new Islemler())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $firinTabloAdi = (new Firinlar())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();
            $malzemeTabloAdi = (new Malzemeler())->getTable();
            $firmaTabloAdi = (new Firmalar())->getTable();
            $islemTuruTabloAdi = (new IslemTurleri())->getTable();
            $siparisDurumTabloAdi = (new SiparisDurumlari())->getTable();

            $alanlar = "
                $islemTabloAdi.*,
                IF(
                    $islemTabloAdi.paraBirimi = 'TL',
                    IF (
                        $islemTabloAdi.miktarFiyatCarp,
                        $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                        $islemTabloAdi.birimFiyat
                    ),
                    0
                ) as tutarTL,
                IF(
                    $islemTabloAdi.paraBirimi = 'USD',
                    IF (
                        $islemTabloAdi.miktarFiyatCarp,
                        $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                        $islemTabloAdi.birimFiyat
                    ),
                    0
                ) as tutarUSD,
                ($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                $islemDurumTabloAdi.ad as islemDurumuAdi,
                $islemDurumTabloAdi.kod as islemDurumuKodu,
                $islemDurumTabloAdi.json as islemDurumuJson,
                $firinTabloAdi.ad as firinAdi,
                $firinTabloAdi.json as firinJson,
                $siparisTabloAdi.firmaId,
                $siparisTabloAdi.siparisNo,
                $siparisTabloAdi.ad as siparisAdi,
                $siparisTabloAdi.terminSuresi,
                $siparisTabloAdi.tarih,
                $malzemeTabloAdi.ad as malzemeAdi,
                $firmaTabloAdi.firmaAdi,
                $islemTuruTabloAdi.ad as islemTuruAdi
            ";

            $islemler = Islemler::select(DB::raw($alanlar))
                ->join($islemDurumTabloAdi, $islemDurumTabloAdi . ".id", "=", $islemTabloAdi . ".durumId")
                ->join($firinTabloAdi, $firinTabloAdi . ".id", "=", $islemTabloAdi . ".firinId")
                ->join($siparisTabloAdi, $siparisTabloAdi . ".id", "=", $islemTabloAdi . ".siparisId")
                ->join($malzemeTabloAdi, $malzemeTabloAdi . ".id", "=", $islemTabloAdi . ".malzemeId")
                ->join($firmaTabloAdi, $firmaTabloAdi . ".id", "=", $siparisTabloAdi . ".firmaId")
                ->join($siparisDurumTabloAdi, $siparisDurumTabloAdi . ".id", "=", $siparisTabloAdi . ".durumId")
                ->leftJoin($islemTuruTabloAdi, $islemTuruTabloAdi . ".id", "=", $islemTabloAdi . ".islemTuruId")
                ->where("$islemTabloAdi.tekrarEdilenId", null)
                ->orderBy("$siparisTabloAdi.siparisNo", "desc")
                ->orderBy("$islemTabloAdi.siraNo", "asc");

            if (isset($filtrelemeler["gecikmisIslemleriGoster"]) && $filtrelemeler["gecikmisIslemleriGoster"])
            {
                $islemler = $islemler->whereRaw("
                    DATEDIFF(DATE(NOW()), $siparisTabloAdi.tarih) > FLOOR($siparisTabloAdi.terminSuresi * 30 / 100)
                    AND $islemTabloAdi.bitisTarihi IS NULL
                    AND $siparisTabloAdi.bitisTarihi IS NULL
                    AND $siparisDurumTabloAdi.kod <> 'TAMAMLANDI'
                    AND $siparisDurumTabloAdi.kod <> 'TAMAMLANDI'
                ");
            }

            if (isset($filtrelemeler["islemId"]) && $filtrelemeler["islemId"])
            {
                $islemler = $islemler->where("$islemTabloAdi.id", $filtrelemeler["islemId"]);
            }

            if (isset($filtrelemeler["firin"]) && $filtrelemeler["firin"] && count($filtrelemeler["firin"]) > 0) {
                $firinIdleri = array_column($filtrelemeler["firin"], "id");

                $islemler = $islemler->whereIn("$islemTabloAdi.firinId", $firinIdleri);
            }

            if (isset($filtrelemeler["islemDurumu"]) && $filtrelemeler["islemDurumu"] && count($filtrelemeler["islemDurumu"]) > 0) {
                $islemDurumIdleri = array_column($filtrelemeler["islemDurumu"], "id");

                $islemler = $islemler->whereIn("$islemTabloAdi.durumId", $islemDurumIdleri);
            }
            else
            {
                $islemler = $islemler->whereIn("$islemDurumTabloAdi.kod", ["ISLEM_BEKLIYOR", "ISLEMDE", "TAMAMLANDI"]);
            }

            if (isset($filtrelemeler["termin"]) && $filtrelemeler["termin"] > 0) {
                $tarih = Carbon::now()->subDays($filtrelemeler["termin"])->format('Y-m-d');

                $islemler = $islemler->where("$siparisTabloAdi.tarih", "<=", $tarih);
            }

            if (isset($filtrelemeler["arama"]) && $filtrelemeler["arama"] != "") {
                // Sipariş no, firin adı, malzeme adı, firma adı, islem durumu adı
                $islemler = $islemler->where(function ($query) use ($filtrelemeler, $firinTabloAdi, $siparisTabloAdi, $malzemeTabloAdi, $firmaTabloAdi, $islemDurumTabloAdi) {
                    $query->where("$siparisTabloAdi.siparisNo", "like", "%" . $filtrelemeler["arama"] . "%")
                        ->orWhere("$firinTabloAdi.ad", "like", "%" . $filtrelemeler["arama"] . "%")
                        ->orWhere("$malzemeTabloAdi.ad", "like", "%" . $filtrelemeler["arama"] . "%")
                        ->orWhere("$firmaTabloAdi.firmaAdi", "like", "%" . $filtrelemeler["arama"] . "%")
                        ->orWhere("$islemDurumTabloAdi.ad", "like", "%" . $filtrelemeler["arama"] . "%");
                });
            }

            if (isset($filtrelemeler["tekrarEdenleriGoster"]) && $filtrelemeler["tekrarEdenleriGoster"]) {
                $islemler = $islemler->where("$islemTabloAdi.tekrarEdenId", "!=", null);
            }

            if (isset($filtrelemeler["baslangicTarihi"]) && $filtrelemeler["baslangicTarihi"])
            {
                $islemler = $islemler->where("$siparisTabloAdi.tarih", ">=", $filtrelemeler["baslangicTarihi"]);
            }

            if (isset($filtrelemeler["bitisTarihi"]) && $filtrelemeler["bitisTarihi"])
            {
                $islemler = $islemler->where("$siparisTabloAdi.tarih", "<=", $filtrelemeler["bitisTarihi"]);
            }

            $islemler = $islemler->paginate($sayfalamaSayisi)->toArray();

            foreach ($islemler["data"] as &$islem)
            {
                $terminBilgileri = $this->terminHesapla($islem["tarih"], $islem["terminSuresi"] ?? 5, $islem["bitisTarihi"]);
                $islem["gecenSure"] = $terminBilgileri["gecenSure"];
                $islem["gecenSureRenk"] = $terminBilgileri["gecenSureRenk"];

                $islem["firinJson"] = json_decode($islem["firinJson"], true);

                if (isset(($islem["firinJson"]["renk"]))) {
                    $islem["firinRenk"] = $islem["firinJson"]["renk"];
                }

                $islem["islemDurumuJson"] = json_decode($islem["islemDurumuJson"], true);

                if (isset(($islem["islemDurumuJson"]["renk"]))) {
                    $islem["islemDurumuRenk"] = $islem["islemDurumuJson"]["renk"];
                    $islem["islemDurumuIkon"] = $islem["islemDurumuJson"]["ikon"];
                }

                $islem["tutarTLYazi"] = $this->yaziyaDonustur($islem["tutarTL"], [
                    "paraBirimi" => $this->paraBirimleri["TL"],
                ]);
                $islem["tutarUSDYazi"] = $this->yaziyaDonustur($islem["tutarUSD"], [
                    "paraBirimi" => $this->paraBirimleri["USD"],
                ]);
                $islem["birimFiyatYazi"] = $this->yaziyaDonustur($islem["birimFiyat"], [
                    "paraBirimi" => $this->paraBirimleri[$islem["paraBirimi"]],
                ]);
                $islem["netYazi"] = $this->yaziyaDonustur($islem["net"], ["kg" => true]);

                if (!isset($islem["tekrarEdenIslemler"])) {
                    $islem["tekrarEdenIslemler"] = [];
                }

                $islem["tekrarEdenIslemler"] = Islemler::where("tekrarEdilenId", $islem["id"])
                    ->select(DB::raw("
                        $islemTabloAdi.*,
                        IF(
                            $islemTabloAdi.paraBirimi = 'TL',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        ) as tutarTL,
                        IF(
                            $islemTabloAdi.paraBirimi = 'USD',
                            IF (
                                $islemTabloAdi.miktarFiyatCarp,
                                $islemTabloAdi.birimFiyat * ($islemTabloAdi.miktar - $islemTabloAdi.dara),
                                $islemTabloAdi.birimFiyat
                            ),
                            0
                        ) as tutarUSD,
                        ($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                        $islemDurumTabloAdi.ad as islemDurumuAdi,
                        $islemDurumTabloAdi.kod as islemDurumuKodu,
                        $islemDurumTabloAdi.json as islemDurumuJson,
                        $firinTabloAdi.ad as firinAdi,
                        $firinTabloAdi.json as firinJson,
                        $siparisTabloAdi.firmaId,
                        $siparisTabloAdi.siparisNo,
                        $siparisTabloAdi.ad as siparisAdi,
                        $siparisTabloAdi.terminSuresi,
                        $siparisTabloAdi.tarih,
                        $malzemeTabloAdi.ad as malzemeAdi,
                        $firmaTabloAdi.firmaAdi
                    "))
                    ->join($islemDurumTabloAdi, $islemDurumTabloAdi . ".id", "=", $islemTabloAdi . ".durumId")
                    ->join($firinTabloAdi, $firinTabloAdi . ".id", "=", $islemTabloAdi . ".firinId")
                    ->join($siparisTabloAdi, $siparisTabloAdi . ".id", "=", $islemTabloAdi . ".siparisId")
                    ->join($malzemeTabloAdi, $malzemeTabloAdi . ".id", "=", $islemTabloAdi . ".malzemeId")
                    ->join($firmaTabloAdi, $firmaTabloAdi . ".id", "=", $siparisTabloAdi . ".firmaId")
                    ->whereIn("$islemDurumTabloAdi.kod", ["ISLEM_BEKLIYOR", "ISLEMDE", "TAMAMLANDI"])
                    ->orderBy("$islemTabloAdi.created_at", "asc")
                    ->get()
                    ->toArray();

                if (count($islem["tekrarEdenIslemler"]) > 0)
                {
                    foreach ($islem["tekrarEdenIslemler"] as &$tekrarEdenIslem)
                    {
                        $terminBilgileri = $this->terminHesapla($tekrarEdenIslem["tarih"], $tekrarEdenIslem["terminSuresi"] ?? 5, $tekrarEdenIslem["bitisTarihi"]);
                        $tekrarEdenIslem["gecenSure"] = $terminBilgileri["gecenSure"];
                        $tekrarEdenIslem["gecenSureRenk"] = $terminBilgileri["gecenSureRenk"];

                        $tekrarEdenIslem["firinJson"] = json_decode($tekrarEdenIslem["firinJson"], true);

                        if (isset(($tekrarEdenIslem["firinJson"]["renk"]))) {
                            $tekrarEdenIslem["firinRenk"] = $tekrarEdenIslem["firinJson"]["renk"];
                        }

                        $tekrarEdenIslem["islemDurumuJson"] = json_decode($tekrarEdenIslem["islemDurumuJson"], true);

                        if (isset(($tekrarEdenIslem["islemDurumuJson"]["renk"]))) {
                            $tekrarEdenIslem["islemDurumuRenk"] = $tekrarEdenIslem["islemDurumuJson"]["renk"];
                            $tekrarEdenIslem["islemDurumuIkon"] = $tekrarEdenIslem["islemDurumuJson"]["ikon"];
                        }

                        $tekrarEdenIslem["tutarTLYazi"] = $this->yaziyaDonustur($tekrarEdenIslem["tutarTL"], [
                            "paraBirimi" => $this->paraBirimleri["TL"],
                        ]);
                        $tekrarEdenIslem["tutarUSDYazi"] = $this->yaziyaDonustur($tekrarEdenIslem["tutarUSD"], [
                            "paraBirimi" => $this->paraBirimleri["USD"],
                        ]);
                        $tekrarEdenIslem["netYazi"] = $this->yaziyaDonustur($tekrarEdenIslem["net"], ["kg" => true]);
                    }
                }
            }

            if ($cikti)
            {
                return (
                    new ExcelExporter($islemler["data"], [
                        "id" => "İşlem ID",
                        "siparisNo" => "Sipariş No",
                        [
                            "key" => "tarih",
                            "value" => "Sipariş Tarihi",
                            "tur" => "TARIH"
                        ],
                        "gecenSure" => "Termin",
                        "firmaAdi" => "Firma",
                        "formId" => "Form ID",
                        "firinAdi" => "Fırın",
                        "sarj" => "Şarj",
                        "islemDurumuAdi" => "İşlem Durumu",
                        "adet" => "Adet",
                        "miktar" => "Miktar",
                        "dara" => "Dara",
                        "netYazi" => "Miktar (Net)",
                        "tutarTLYazi" => "Tutar (TL)",
                        "tutarUSDYazi" => "Tutar (USD)",
                        "malzemeAdi" => "Malzeme",
                        "istenilenSertlik" => "İst. Sertlik",
                        "kalite" => "Kalite",
                        "sicaklik" => "Sıcaklık",
                        "carbon" => "Carbon",
                        "beklenenSure" => "Süre",
                        "cikisSertligi" => "Ç. Sertliği",
                        "menevisSicakligi" => "Men. Sıcaklığı",
                        "cikisSuresi" => "Süre",
                        "sonSertlik" => "Son Sertlik",
                    ])
                )->downloadExcel("İşlem Listesi");
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'İşlemler listelendi.',
                'islemler' => $islemler,
            ], 200);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                'satir' => $e->getLine(),
            ], 500);
        }
    }

    public function islemDurumuDegistir(Request $request)
    {
        DB::beginTransaction();

        try {
            $kullaniciId = $request->kullaniciId ?? auth()->user()->id;
            $kullaniciAdi = User::find($kullaniciId)->name;
            $islemBilgileri = $request->islem;
            $islemDurumuKodu = $request->islemDurumuKodu;

            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();

            $islemDurum = IslemDurumlari::where("kod", $islemDurumuKodu)->first();

            $islem = Islemler::where("id", $islemBilgileri["id"])->first();
            $siparis = Siparisler::where("id", $islem->siparisId)->first();

            if (!$islem) {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem bulunamadı.',
                    "hataKodu" => "F005",
                ], 500);
            }

            $durumIcinIslemModel = Islemler::select(
                    "$islemTabloAdi.*", "$islemDurumTabloAdi.ad as islemDurumuAdi",
                    "$islemDurumTabloAdi.kod as islemDurumuKodu",
                    "$islemDurumTabloAdi.json as islemDurumuJson"
                )
                ->join($islemDurumTabloAdi, $islemDurumTabloAdi . ".id", "=", "$islemTabloAdi.durumId")
                ->where("$islemTabloAdi.id", $islemBilgileri["id"])
                ->first()
                ->toArray();

            // İşlemin önceki durumu ISLEM_BEKLIYOR ise baslangicTarihi alanını ayarlıyoruz
            if ($durumIcinIslemModel["islemDurumuKodu"] === "ISLEM_BEKLIYOR")
            {
                $islem->baslangicTarihi = Carbon::now();

                $siparisIslemdeDurum = SiparisDurumlari::where("kod", "ISLEMDE")->first();
                $siparis->durumId = $siparisIslemdeDurum->id;
            }
            // İşlemin önceki durumu TAMAMLANDI ise bitisTarihi alanını null yapıyoruz
            else if ($durumIcinIslemModel["islemDurumuKodu"] === "TAMAMLANDI")
            {
                $islem->bitisTarihi = null;

                $siparisTamamlandiDurum = SiparisDurumlari::where("kod", "ISLEMDE")->first();
                $siparis->durumId = $siparisTamamlandiDurum->id;
            }

            if ($islemDurumuKodu === "TAMAMLANDI")
            {
                $islem->bitisTarihi = Carbon::now();

                $siparisTamamlandiDurum = SiparisDurumlari::where("kod", "TAMAMLANDI")->first();
                $siparis->durumId = $siparisTamamlandiDurum->id;
            }

            $islem->durumId = $islemDurum->id;

            if (!$islem->save()) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem kaydedilemedi.',
                    "hataKodu" => "F006",
                ], 500);
            }

            if (!$siparis->save()) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Sipariş kaydedilemedi.',
                    "hataKodu" => "F007",
                ], 500);
            }

            // Bildirim atılıyor
            $bildirimDurum = $this->bildirimAt($kullaniciId, [
                "baslik" => "İşlem Durumu Değiştirildi",
                "icerik" => "'$islem->id' numaralı idye ait işlem durumu, '$islemDurum->ad' olarak değiştirildi.",
                "link" => "/tum-islemler?islemId=$islem->id",
                "kod" => "ISLEM_DURUMU_BILDIRIMI",
                "actionId" => $islem->id,
            ]);

            // if (!$bildirimDurum)
            // {
            //     DB::rollBack();

            //     return response()->json([
            //         'durum' => false,
            //         'mesaj' => 'İşlem bildirim atılamadı.',
            //         "hataKodu" => "F009",
            //     ], 500);
            // }

            if (!$this->islemBitisTarihleriAyarla($islem->id)) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem bitiş tarihleri ayarlanamadı.',
                    "hataKodu" => "IBT001",
                ], 500);
            }

            DB::commit();

            return response()->json([
                'durum' => true,
                'mesaj' => 'İşlem durumu \'' . $islemDurum->ad . '\' olarak değiştirildi.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                'satir' => $e->getLine(),
            ], 500);
        }
    }

    public function islemTekrarEt(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $kullaniciId = $request->kullaniciId ?? auth()->user()->id;
            $kullaniciAdi = User::find($kullaniciId)->name;
            $islemBilgileri = $request->islem;
            $formId = $request->formId ?? null;

            $islemTabloAdi = (new Islemler())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();

            $islem = Islemler::where("id", $islemBilgileri["id"])->first();

            if (!$islem) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem bulunamadı.',
                    "hataKodu" => "F005",
                ], 500);
            }

            $yeniIslem = $islem->replicate();

            $baslanmadiDurum = IslemDurumlari::where("kod", "BASLANMADI")->first();

            if (!$baslanmadiDurum) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem başlanmadı durumu bulunamadı.',
                    "hataKodu" => "F008",
                ], 500);
            }

            $yeniIslem->tekrarEdilenId = $islem->tekrarEdilenId ?? $islem->id;
            $yeniIslem->durumId = $baslanmadiDurum->id;
            $yeniIslem->formId = $formId;
            $yeniIslem->firinId = null;
            $yeniIslem->sarj = null;
            $yeniIslem->baslangicTarihi = null;
            $yeniIslem->bitisTarihi = null;
            $yeniIslem->birimFiyat = 0;

            if (!$yeniIslem->save()) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem tekrar edilemedi.',
                    "hataKodu" => "F006",
                ], 500);
            }

            $tamamlandiDurum = IslemDurumlari::where("kod", "TAMAMLANDI")->first();

            if (!$tamamlandiDurum) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem tamamlandı durumu bulunamadı.',
                    "hataKodu" => "F008",
                ], 500);
            }

            $islem->durumId = $tamamlandiDurum->id;
            $islem->tekrarEdenId = $yeniIslem->id;
            $islem->aciklama = $islemBilgileri["aciklama"] ?? null;

            if (!$islem->save()) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem kaydedilemedi.',
                    "hataKodu" => "F007",
                ], 500);
            }

            // Bildirim atılıyor
            $islemBilgi = Islemler::selectRaw("$islemTabloAdi.*, $siparisTabloAdi.tarih, $siparisTabloAdi.terminSuresi")
                ->join($siparisTabloAdi, "$siparisTabloAdi.id", "=", "$islemTabloAdi.siparisId")
                ->where("$islemTabloAdi.id", $islemBilgileri["id"])
                ->first();

            $terminBilgileri = $this->terminHesapla($islemBilgi->tarih, $islemBilgi->terminSuresi);
            $gecenSure = $terminBilgileri["gecenSure"];

            $bildirimDurum = $this->bildirimAt($kullaniciId, [
                "baslik" => "İşlem Tekrar Edildi",
                "icerik" => "$islemBilgi->id numaralı idye ait işlem, $kullaniciAdi adlı kullanıcı tarafından tekrar edildi. Termin: $gecenSure Gün",
                "link" => "/tum-islemler?islemId=$islem->id",
                "kod" => "ISLEM_DURUMU_BILDIRIMI",
                "actionId" => $islem->id,
            ]);

            // if (!$bildirimDurum)
            // {
            //     DB::rollBack();

            //     return response()->json([
            //         'durum' => false,
            //         'mesaj' => 'İşlem bildirim atılamadı.',
            //         "hataKodu" => "F009",
            //     ], 500);
            // }

            if (!$this->islemBitisTarihleriAyarla($islem->id)) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem bitiş tarihleri ayarlanamadı.',
                    "hataKodu" => "IBT001",
                ], 500);
            }

            DB::commit();

            return response()->json([
                'durum' => true,
                'mesaj' => 'İşlem tekrar edildi.',
            ], 200);
        }
        catch (\Exception $e)
        {
            DB::rollBack();

            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                'satir' => $e->getLine(),
            ], 500);
        }
    }

    public function islemTamamlandiGeriAl(Request $request)
    {
        DB::beginTransaction();

        try {
            $islemBilgileri = $request->islem;

            $islemTabloAdi = (new Islemler())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();

            $islem = Islemler::where("id", $islemBilgileri["id"])->first();

            if (!$islem) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem bulunamadı.',
                    "hataKodu" => "ITG001",
                ], 500);
            }

            // İşlem tekrar edilerek tamamlandıysa
            if ($islem->tekrarEdenId) {
                $tekrarEdenIslem = Islemler::select(DB::raw("$islemTabloAdi.*, $islemDurumTabloAdi.ad as islemDurumuAdi, $islemDurumTabloAdi.kod as islemDurumuKodu"))
                    ->join($islemDurumTabloAdi, $islemDurumTabloAdi . ".id", "=", $islemTabloAdi . ".durumId")
                    ->where("$islemTabloAdi.id", $islem->tekrarEdenId)
                    ->first();

                if (in_array($tekrarEdenIslem->islemDurumuKodu, ["ISLEMDE", "TAMAMLANDI"])) {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'Tekrar edilen işlem durumu \'' . $tekrarEdenIslem->islemDurumuAdi . '\' olduğu için işlem geri alınamaz.',
                        "hataKodu" => "ITG002",
                    ], 500);
                }

                if (!$tekrarEdenIslem->delete()) {
                    DB::rollBack();

                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'Tekrar edilen işlem silinemedi.',
                        "hataKodu" => "ITG003",
                    ], 500);
                }

                $islem->tekrarEdenId = null;
            }

            $islemdeDurum = IslemDurumlari::where("kod", "ISLEMDE")->first();

            if (!$islemdeDurum) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlemde durumu bulunamadı.',
                    "hataKodu" => "ITG004",
                ], 500);
            }

            $islem->durumId = $islemdeDurum->id;
            $islem->bitisTarihi = null;

            if (!$islem->save()) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem kaydedilemedi.',
                    "hataKodu" => "ITG005",
                ], 500);
            }

            $siparisTamamlandiDurum = SiparisDurumlari::where("kod", "ISLEMDE")->first();

            $siparis = Siparisler::where("id", $islem->siparisId)->first();
            $siparis->durumId = $siparisTamamlandiDurum->id;

            if (!$siparis->save()) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Sipariş durumu güncellenemedi.',
                    "hataKodu" => "ITG007",
                ], 500);
            }

            if (!$this->islemBitisTarihleriAyarla($islem->id)) {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem bitiş tarihleri ayarlanamadı.',
                    "hataKodu" => "ITG006",
                ], 500);
            }

            DB::commit();

            return response()->json([
                'durum' => true,
                'mesaj' => 'İşlem geri alındı.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                'satir' => $e->getLine(),
            ], 500);
        }
    }

    public function toplamIslem()
    {
        try {
            $toplamIslem = Islemler::count();

            return response()->json([
                "durum" => true,
                "mesaj" => "Toplam işlem sayısı başarılı bir şekilde getirildi.",
                "toplamIslem" => $toplamIslem,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "durum" => false,
                "mesaj" => "Toplam işlem sayısı getirilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }

    public function firinSarjGrupluIslemleriGetir(Request $request)
    {
        try {
            $formId = $request->formId;

            $siparisTabloAdi = (new Siparisler())->getTable();
            $islemTabloAdi = (new Islemler())->getTable();
            $firmaTabloAdi = (new Firmalar())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $malzemeTabloAdi = (new Malzemeler())->getTable();
            $islemTuruTabloAdi = (new IslemTurleri())->getTable();
            $firinTabloAdi = (new Firinlar())->getTable();

            $firinSarjGrupluIslemler = Islemler::selectRaw("
                $islemTabloAdi.id,
                $islemTabloAdi.siparisId,
                $islemTabloAdi.malzemeId,
                $islemTabloAdi.formId,
                $islemTabloAdi.firinId,
                $islemTabloAdi.sarj,
                $islemTabloAdi.islemTuruId,
                $islemTabloAdi.durumId as islemDurumId,
                $islemTabloAdi.bolunmusId,
                $islemTabloAdi.adet,
                $islemTabloAdi.miktar,
                $islemTabloAdi.dara,
                ($islemTabloAdi.miktar - $islemTabloAdi.dara) as net,
                $islemTabloAdi.kalite,
                $islemTabloAdi.istenilenSertlik,
                $islemTabloAdi.sicaklik,
                $islemTabloAdi.carbon,
                $islemTabloAdi.beklenenSure,
                $islemTabloAdi.cikisSertligi,
                $islemTabloAdi.menevisSicakligi,
                $islemTabloAdi.cikisSuresi,
                $islemTabloAdi.sonSertlik,
                $islemTabloAdi.tekrarEdenId,
                $islemTabloAdi.tekrarEdilenId,
                $islemTabloAdi.resimYolu,
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
                $islemDurumTabloAdi.ad as islemDurumuAdi,
                $islemDurumTabloAdi.kod as islemDurumuKodu,
                $islemDurumTabloAdi.json as islemDurumuJson,
                $malzemeTabloAdi.ad as malzemeAdi,
                $islemTuruTabloAdi.ad as islemTuruAdi,
                $firinTabloAdi.ad as firinAdi,
                $firinTabloAdi.kod as firinKodu,
                $firinTabloAdi.json as firinJson
            ")
                ->join($siparisTabloAdi, $siparisTabloAdi . '.id', '=', $islemTabloAdi . '.siparisId')
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($islemDurumTabloAdi, $islemDurumTabloAdi . '.id', '=', $islemTabloAdi . '.durumId')
                ->join($malzemeTabloAdi, $malzemeTabloAdi . '.id', '=', $islemTabloAdi . '.malzemeId')
                ->join($firinTabloAdi, $firinTabloAdi . '.id', '=', $islemTabloAdi . '.firinId')
                ->leftJoin($islemTuruTabloAdi, $islemTuruTabloAdi . '.id', '=', $islemTabloAdi . '.islemTuruId')
                ->where($islemTabloAdi . '.formId', $formId)
                ->get()
                ->toArray();

            $islemler = [];
            foreach ($firinSarjGrupluIslemler as $islem) {
                $islem["firinJson"] = json_decode($islem["firinJson"], true);
                $islem["islemDurumuJson"] = json_decode($islem["islemDurumuJson"], true);

                if (isset(($islem["islemDurumuJson"]["renk"])))
                {
                    $islem["islemDurumuRenk"] = $islem["islemDurumuJson"]["renk"];
                    $islem["islemDurumuIkon"] = $islem["islemDurumuJson"]["ikon"];
                }

                $terminDizisi = $this->terminHesapla($islem["siparisTarihi"], $islem["terminSuresi"] ?? 5);
                $islem["gecenSure"] = $terminDizisi["gecenSure"];
                $islem["gecenSureRenk"] = $terminDizisi["gecenSureRenk"];

                $islem["sarj"] = $islem["sarj"] ?? 1;

                if (!isset($islemler[$islem["firinId"]])) {
                    $islemler[$islem["firinId"]] = [
                        "firinId" => $islem["firinId"],
                        "firinAdi" => $islem["firinAdi"],
                        "firinKodu" => $islem["firinKodu"],
                        "firinJson" => $islem["firinJson"],
                        "sarjlar" => [],
                    ];
                }

                if (!isset($islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]])) {
                    $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]] = [
                        "sarj" => $islem["sarj"],
                        "islemler" => [],
                    ];
                }

                if (!isset($islem["tekrarEdenIslemler"]))
                {
                    $islem["tekrarEdenIslemler"] = [];
                }

                // Tekrar eden işlemler
                $islem["tekrarEdenIslemler"] = Islemler::where("tekrarEdilenId", $islem["id"])
                    ->select(DB::raw("
                        $islemTabloAdi.*,
                        $islemDurumTabloAdi.ad as islemDurumuAdi,
                        $islemDurumTabloAdi.kod as islemDurumuKodu,
                        $islemDurumTabloAdi.json as islemDurumuJson,
                        $firinTabloAdi.ad as firinAdi,
                        $firinTabloAdi.json as firinJson,
                        $siparisTabloAdi.firmaId,
                        $siparisTabloAdi.siparisNo,
                        $siparisTabloAdi.ad as siparisAdi,
                        $siparisTabloAdi.terminSuresi,
                        $siparisTabloAdi.tarih,
                        $malzemeTabloAdi.ad as malzemeAdi,
                        $firmaTabloAdi.firmaAdi
                    "))
                    ->join($islemDurumTabloAdi, $islemDurumTabloAdi . ".id", "=", $islemTabloAdi . ".durumId")
                    ->join($firinTabloAdi, $firinTabloAdi . ".id", "=", $islemTabloAdi . ".firinId")
                    ->join($siparisTabloAdi, $siparisTabloAdi . ".id", "=", $islemTabloAdi . ".siparisId")
                    ->join($malzemeTabloAdi, $malzemeTabloAdi . ".id", "=", $islemTabloAdi . ".malzemeId")
                    ->join($firmaTabloAdi, $firmaTabloAdi . ".id", "=", $siparisTabloAdi . ".firmaId")
                    ->whereIn("$islemDurumTabloAdi.kod", ["ISLEM_BEKLIYOR", "ISLEMDE", "TAMAMLANDI"])
                    ->orderBy("$islemTabloAdi.created_at", "asc")
                    ->get()
                    ->toArray();

                if (count($islem["tekrarEdenIslemler"]) > 0)
                {
                    foreach ($islem["tekrarEdenIslemler"] as &$tekrarEdenIslem)
                    {
                        $terminBilgileri = $this->terminHesapla($tekrarEdenIslem["tarih"], $tekrarEdenIslem["terminSuresi"] ?? 5);
                        $tekrarEdenIslem["gecenSure"] = $terminBilgileri["gecenSure"];
                        $tekrarEdenIslem["gecenSureRenk"] = $terminBilgileri["gecenSureRenk"];

                        $tekrarEdenIslem["firinJson"] = json_decode($tekrarEdenIslem["firinJson"], true);

                        if (isset(($tekrarEdenIslem["firinJson"]["renk"])))
                        {
                            $tekrarEdenIslem["firinRenk"] = $tekrarEdenIslem["firinJson"]["renk"];
                        }

                        $tekrarEdenIslem["islemDurumuJson"] = json_decode($tekrarEdenIslem["islemDurumuJson"], true);

                        if (isset(($tekrarEdenIslem["islemDurumuJson"]["renk"])))
                        {
                            $tekrarEdenIslem["islemDurumuRenk"] = $tekrarEdenIslem["islemDurumuJson"]["renk"];
                            $tekrarEdenIslem["islemDurumuIkon"] = $tekrarEdenIslem["islemDurumuJson"]["ikon"];
                        }
                    }
                }

                $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemler"][] = $islem;

                // Şarjın durumu ve fırının durumunu tespit ediyoruz
                $islemIcinDurumlar = array_count_values(array_column($islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemler"], 'islemDurumuKodu'));

                $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemdekiIslemSayisi"] = $islemIcinDurumlar["ISLEMDE"] ?? 0;
                $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["bekleyenIslemSayisi"] = $islemIcinDurumlar["ISLEM_BEKLIYOR"] ?? 0;
                $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["tamamlananIslemSayisi"] = $islemIcinDurumlar["TAMAMLANDI"] ?? 0;

                if ($islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemdekiIslemSayisi"] > 0)
                {
                    $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemDurumuKodu"] = "ISLEMDE";
                    $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemDurumAdi"] = "İşlemde";
                }
                else if ($islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["bekleyenIslemSayisi"] > 0)
                {
                    $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemDurumuKodu"] = "ISLEM_BEKLIYOR";
                    $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemDurumAdi"] = "İşlem Bekliyor";
                }
                else if ($islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["tamamlananIslemSayisi"] > 0)
                {
                    $toplamIslemSayisi = count($islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemler"]);
                    if ($islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["tamamlananIslemSayisi"] === $toplamIslemSayisi)
                    {
                        $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemDurumuKodu"] = "TAMAMLANDI";
                        $islemler[$islem["firinId"]]["sarjlar"][$islem["sarj"]]["islemDurumAdi"] = "Tamamlandı";
                    }
                }

                $sarjIcinDurumlar = array_count_values(array_column($islemler[$islem["firinId"]]["sarjlar"], 'islemDurumuKodu'));

                $islemler[$islem["firinId"]]["islemdekiSarjSayisi"] = $sarjIcinDurumlar["ISLEMDE"] ?? 0;
                $islemler[$islem["firinId"]]["bekleyenSarjSayisi"] = $sarjIcinDurumlar["ISLEM_BEKLIYOR"] ?? 0;
                $islemler[$islem["firinId"]]["tamamlananSarjSayisi"] = $sarjIcinDurumlar["TAMAMLANDI"] ?? 0;

                if ($islemler[$islem["firinId"]]["islemdekiSarjSayisi"] > 0)
                {
                    $islemler[$islem["firinId"]]["islemDurumuKodu"] = "ISLEMDE";
                    $islemler[$islem["firinId"]]["islemDurumAdi"] = "İşlemde";
                }
                else if ($islemler[$islem["firinId"]]["bekleyenSarjSayisi"] > 0)
                {
                    $islemler[$islem["firinId"]]["islemDurumuKodu"] = "ISLEM_BEKLIYOR";
                    $islemler[$islem["firinId"]]["islemDurumAdi"] = "İşlem Bekliyor";
                }
                else if ($islemler[$islem["firinId"]]["tamamlananSarjSayisi"] > 0)
                {
                    $toplamSarjSayisi = count($islemler[$islem["firinId"]]["sarjlar"]);
                    if ($islemler[$islem["firinId"]]["tamamlananSarjSayisi"] === $toplamSarjSayisi)
                    {
                        $islemler[$islem["firinId"]]["islemDurumuKodu"] = "TAMAMLANDI";
                        $islemler[$islem["firinId"]]["islemDurumAdi"] = "Tamamlandı";
                    }
                }
            }

            return response()->json([
                "durum" => true,
                "mesaj" => "İşlemler başarılı bir şekilde getirildi.",
                "firinSarjGrupluIslemler" => array_values($islemler),
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "durum" => false,
                "mesaj" => "İşlemler getirilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }

    public function islemDetay(Request $request)
    {
        try {
            $islemId = $request->islemId;

            $islemTabloAdi = (new Islemler())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();
            $firmaTabloAdi = (new Firmalar())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $malzemeTabloAdi = (new Malzemeler())->getTable();
            $islemTuruTabloAdi = (new IslemTurleri())->getTable();
            $firinTabloAdi = (new Firinlar())->getTable();

            $islem = Islemler::selectRaw("
                $islemTabloAdi.*,
                $siparisTabloAdi.firmaId,
                $siparisTabloAdi.durumId,
                $siparisTabloAdi.ad as siparisAdi,
                $siparisTabloAdi.siparisNo,
                $siparisTabloAdi.aciklama,
                $siparisTabloAdi.terminSuresi,
                $siparisTabloAdi.tarih as siparisTarihi,
                $firmaTabloAdi.firmaAdi,
                $firmaTabloAdi.sorumluKisi,
                $islemDurumTabloAdi.ad as islemDurumAdi,
                $malzemeTabloAdi.ad as malzemeAdi,
                $islemTuruTabloAdi.ad as islemTuruAdi,
                $firinTabloAdi.ad as firinAdi,
                $firinTabloAdi.kod as firinKodu,
                $firinTabloAdi.json as firinJson
            ")
                ->join($siparisTabloAdi, $siparisTabloAdi . '.id', '=', $islemTabloAdi . '.siparisId')
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($islemDurumTabloAdi, $islemDurumTabloAdi . '.id', '=', $islemTabloAdi . '.durumId')
                ->join($malzemeTabloAdi, $malzemeTabloAdi . '.id', '=', $islemTabloAdi . '.malzemeId')
                ->join($firinTabloAdi, $firinTabloAdi . '.id', '=', $islemTabloAdi . '.firinId')
                ->leftJoin($islemTuruTabloAdi, $islemTuruTabloAdi . '.id', '=', $islemTabloAdi . '.islemTuruId')
                ->where($islemTabloAdi . '.id', $islemId)
                ->first()
                ->toArray();

            $islem["firinJson"] = json_decode($islem["firinJson"], true);

            $terminDizisi = $this->terminHesapla($islem["siparisTarihi"], $islem["terminSuresi"] ?? 5);
            $islem["gecenSure"] = $terminDizisi["gecenSure"];
            $islem["gecenSureRenk"] = $terminDizisi["gecenSureRenk"];

            return response()->json([
                "durum" => true,
                "mesaj" => "İşlem detayı başarılı bir şekilde getirildi.",
                "islem" => $islem,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "durum" => false,
                "mesaj" => "İşlem detayı getirilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }

    public function sarjIslemleriBaslat(Request $request)
    {
        try
        {
            $firin = $request->firin;
            $firinId = $firin["firinId"];
            $firinAdi = $firin["firinAdi"];
            $sarj = $request->sarj;
            $formId = $request->formId;
            // $islemTabloAdi = (new Islemler())->getTable();
            $userId = auth()->user()->id;

            $islemdeIslemDurumu = IslemDurumlari::where("kod", "ISLEMDE")->first();
            $tamamlandiIslemDurumu = IslemDurumlari::where("kod", "TAMAMLANDI")->first();

            $islemUpdateDurum = Islemler::where("firinId", $firinId)
                ->where("sarj", $sarj)
                ->where("durumId", "<>", $tamamlandiIslemDurumu->id)
                ->update([
                    "durumId" => $islemdeIslemDurumu->id,
                    "baslangicTarihi" => Carbon::now(),
                ]);

            if (!$islemUpdateDurum)
            {
                return response()->json([
                    "durum" => false,
                    "mesaj" => "Sarj işlemleri başlatılırken bir hata oluştu.",
                    "hata" => "İşlemler güncellenemedi.",
                ], 500);
            }

            $this->formDurumKontrol($formId);

            $bildirimDurum = $this->bildirimAt($userId, [
                "baslik" => "Sarj işlemleri başlatıldı",
                "icerik" => "$firinAdi'ın $sarj. sarj işlemleri başlatıldı.",
                "link" => "/isil-islemler?formId=$formId&firinId=$firinId&sarj=$sarj",
                "kod" => "FORM_BILDIRIMI",
                "actionId" => $formId,
            ]);

            return response()->json([
                "durum" => true,
                "mesaj" => "Sarj islemleri başlatıldı.",
            ], 200);
        }
        catch (\Exception $ex)
        {
            return response()->json([
                "durum" => false,
                "mesaj" => "Sarj islemleri başlatılırken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }

    public function sarjIslemleriTamamla(Request $request)
    {
        try
        {
            $firin = $request->firin;
            $firinId = $firin["firinId"];
            $firinAdi = $firin["firinAdi"];
            $sarj = $request->sarj;
            $formId = $request->formId;
            // $islemTabloAdi = (new Islemler())->getTable();
            $userId = auth()->user()->id;

            $tamamlandiIslemDurumu = IslemDurumlari::where("kod", "TAMAMLANDI")->first();

            $islemUpdateDurum = Islemler::where("firinId", $firinId)
                ->where("sarj", $sarj)
                ->where("durumId", "<>", $tamamlandiIslemDurumu->id)
                ->update([
                    "durumId" => $tamamlandiIslemDurumu->id,
                    "bitisTarihi" => Carbon::now(),
                ]);

            if (!$islemUpdateDurum)
            {
                return response()->json([
                    "durum" => false,
                    "mesaj" => "Sarj işlemleri tamamlanırken bir hata oluştu.",
                    "hata" => "İşlemler güncellenemedi.",
                ], 500);
            }

            $bildirimDurum = $this->bildirimAt($userId, [
                "baslik" => "Şarj işlemleri tamamlandı",
                "icerik" => "$firinAdi'ın $sarj. sarj işlemleri tamamlandı.",
                "link" => "/isil-islemler?formId=$formId&firinId=$firinId&sarj=$sarj",
                "kod" => "FORM_BILDIRIMI",
                "actionId" => $formId,
            ]);

            $this->formDurumKontrol($formId);

            return response()->json([
                "durum" => true,
                "mesaj" => "Şarj işlemleri tamamlandı.",
            ], 200);
        }
        catch (\Exception $ex)
        {
            return response()->json([
                "durum" => false,
                "mesaj" => "Sarj islemleri tamamlanırken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }

    public function islemBol(Request $request)
    {
        try
        {
            $islem = $request->islem;
            $bolunmusIslemler = $request->bolunmusIslemler;

            $islemId = $islem["id"];

            $islemBilgisi = Islemler::find($islemId);

            $donecekIslemler = [];
            foreach ($bolunmusIslemler as $bolunmusIslem)
            {
                if (isset($bolunmusIslem["yeniIslem"]) && $bolunmusIslem["yeniIslem"])
                {
                    $yeniIslem = $islemBilgisi->replicate();
                    $yeniIslem->birimFiyat = 0;
                    $yeniIslem->miktar = $islemBilgisi->miktar * $bolunmusIslem["yuzde"] / 100;
                    $yeniIslem->dara = $islemBilgisi->dara * $bolunmusIslem["yuzde"] / 100;
                    $yeniIslem->adet = 0;
                    $yeniIslem->firinId = null;
                    $yeniIslem->sarj = null;
                    $yeniIslem->formId = null;
                    $yeniIslem->bolunmusId = $islemId;

                    if (!$yeniIslem->save())
                    {
                        return response()->json([
                            "durum" => false,
                            "mesaj" => "İşlem bölümünde bir hata oluştu.",
                            "hata" => "İşlem kaydedilemedi.",
                            "hataKodu" => "ISLEM_BOL_HATA_1",
                        ], 500);
                    }

                    $donecekIslemler[] = $yeniIslem;
                }
                else
                {
                    $anaIslemMiktar = $islemBilgisi->miktar * $bolunmusIslem["yuzde"] / 100;
                    $anaIslemDara = $islemBilgisi->dara * $bolunmusIslem["yuzde"] / 100;
                }
            }

            $islemBilgisi->miktar = $anaIslemMiktar;
            $islemBilgisi->dara = $anaIslemDara;

            if (!$islemBilgisi->save())
            {
                return response()->json([
                    "durum" => false,
                    "mesaj" => "İşlem bölümünde bir hata oluştu.",
                    "hata" => "İşlem güncellenemedi.",
                    "hataKodu" => "ISLEM_BOL_HATA_2",
                ], 500);
            }

            array_unshift($donecekIslemler, $islemBilgisi);

            return response()->json([
                "durum" => true,
                "mesaj" => "İşlem bölme işlemi başarılı.",
                "islemler" => $donecekIslemler,
            ], 200);
        }
        catch (\Exception $ex)
        {
            return response()->json([
                "durum" => false,
                "mesaj" => "İşlem bölünürken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "hataKodu" => "ISLEM_BOL_HATA_CATCH",
                "satir" => $ex->getLine(),
            ], 500);
        }
    }
}
