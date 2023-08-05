<?php

namespace App\Http\Controllers;

use App\Models\Firmalar;
use App\Models\Sablonlar;
use App\Models\Teklifler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeklifController extends Controller
{
    public function index($firmaId = null)
    {
        $sablonlars = Sablonlar::all();
        $sablonlar = [];
        foreach ($sablonlars as $sablon)
        {
            $sablonlar[] = [
                "id" => $sablon->id,
                "ad" => $sablon->sablonAdi,
                "tur" => $sablon->tur,
            ];
        }
        return view("teklifler")->with(["sablonlar" => $sablonlar, "firmaId" => $firmaId]);
    }
 /**
     * @global
     */
    public function teklifleriGetir(Request $request)
    {
        try {

            $filtreleme = isset($request->filtreleme) ? json_decode($request->filtreleme, true) : [];
            $sayfalama = $request->sayfalama ?? false;
            $firmaTabloAdi = (new Firmalar())->getTable();
            $teklifTabloAdi = (new Teklifler())->getTable();

            $teklifler = Teklifler::select('teklifler.*', 'firmalar.id as firmaId', 'firmalar.firmaAdi as firmaAdi', 'firmalar.eposta as eposta')
                ->join('firmalar', 'firmalar.id', '=', 'teklifler.firmaId');

            $alan = isset($filtreleme["siralamaTuru"]) && $filtreleme["siralamaTuru"] != null ? array_keys($filtreleme["siralamaTuru"])[0] : "created_at";
            $siralamaTuru = isset($filtreleme["siralamaTuru"]) && $filtreleme["siralamaTuru"] != null ? array_values($filtreleme["siralamaTuru"])[0] : "desc";
            $teklifler->orderBy($alan, $siralamaTuru);

            if ($filtreleme) {

                if (isset($filtreleme["arama"]) && $filtreleme["arama"] != "")
                {
                    $teklifler->where($teklifTabloAdi.".teklifAdi", "like", "%" . $filtreleme["arama"] . "%")
                        ->orWhere($teklifTabloAdi.".id", "like", "%" . $filtreleme["arama"] . "%")
                        ->orWhere($teklifTabloAdi.".tur", "like", "%" . $filtreleme["arama"] . "%")
                        ->orwhere($teklifTabloAdi.".created_at", "like", "%" . $filtreleme["arama"] . "%");
                }
                if (isset($filtreleme["firmaAdi"]) && $filtreleme["firmaAdi"] != "")
                {
                    $teklifler->where($teklifTabloAdi.".firmaAdi", "like", "%" . $filtreleme["firmaAdi"] . "%");
                }
                if (isset($filtreleme["firmaId"]) && $filtreleme["firmaId"] != "")
                {
                    $teklifler->where($teklifTabloAdi.".firmaId", $filtreleme["firmaId"]);
                }

                if (isset($filtreleme["tur"]) && $filtreleme["tur"] != "")
                {
                    $teklifler->whereIn($teklifTabloAdi.".tur", array_column($filtreleme["tur"],"tur") . "%");
                }
                if (isset($filtreleme["teklifAdi"]) && $filtreleme["teklifAdi"] != "")
                {
                    $teklifler->where($teklifTabloAdi.".teklifAdi", "like", "%". $filtreleme["teklifAdi"] . "%");
                }

                if (isset($filtreleme["baslangicTarihi"]) && $filtreleme["baslangicTarihi"] != "")
                {
                    $teklifler->where($teklifTabloAdi.".created_at", ">=", $filtreleme["baslangicTarihi"]);

                    if (isset($filtreleme["bitisTarihi"]) && $filtreleme["bitisTarihi"] != "")
                    {
                        $teklifler->where($teklifTabloAdi.".created_at", "<=", $filtreleme["bitisTarihi"]);
                    }
                }
                else if (isset($filtreleme["bitisTarihi"]) && $filtreleme["bitisTarihi"] != "")
                {
                    $teklifler->where($teklifTabloAdi.".created_at", "<=", $filtreleme["bitisTarihi"]);
                }
                if (isset($filtreleme["topluTeklifleriGetir"]) && $filtreleme["topluTeklifleriGetir"] != "")
                {
                    $teklifler->where($teklifTabloAdi.".topluKey" , '!=', null );
                    // $teklifler->groupBy($teklifTabloAdi.".topluKey")->map(function ($grup) {
                    //     $firmaIdler = $grup->pluck('firmaId')->toArray();
                    //     return $firmaIdler;
                    // })->toArray();

                }

            }

            $teklifler = $teklifler->paginate($request->sayfalamaSayisi ?? 10);


            return response()->json([
                'durum' => true,
                'mesaj' => 'Teklifler başarıyla getirildi.',
                'teklifler' => $teklifler
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }
    public function teklifEkle(Request $request)
    {
        try {
            $firmaId = $request->firmaId;
            $topluKey = $request->topluKey ?? null;
            $tur = $request->tur;
            $html = $request->html;
            $teklifBilgileri = $request->teklifBilgileri;

            $teklif = new Teklifler();
            $teklif->firmaId = $firmaId;
            $teklif->topluKey = $topluKey;
            $teklif->teklifAdi = $this->buyukHarf($teklifBilgileri["firma"] . " " . $tur . " Formu");
            $teklif->tur = $tur;
            $teklif->icerik_html = isset($html) && $html ? $html : null;


            if (!$teklif->save()) {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Teklif eklenemedi.',
                    "hataKodu" => "F005",
                ], 500);
            }else{
                $dosyaAdi = str_replace(' ', '',$request->dosyaAdi);
                $altKlasor = $tur . "/";
                if (isset($teklifBilgileri["klasor"]) && $teklifBilgileri["klasor"])
                {
                    $altKlasor .= $teklifBilgileri["klasor"] . "/";
                }

                $pdf = $this->pdfOlustur2($dosyaAdi,$altKlasor, [
                    "tur" => $tur,
                    "id" => $teklif->id,
                    "payload" => [
                        "renderSettings" => [
                            "emulateMedia" => 'print',
                            "pdfOptions" => [
                                "format" => 'letter',
                                "preferCSSPageSize" => true,
                                "margin" => [
                                    "top" => 0,
                                    "bottom" => 0,
                                    "left" => 0,
                                    "right" => 0
                                ],
                                "zoomFactor" => 1,
                            ],
                        ],
                    ],
                ]);

                if ($pdf === false)
                {
                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'PDF oluşturulurken bir hata oluştu!',
                        "hataKodu" => "CPDF002",
                    ], 400);
                }

                $teklifGuncel = Teklifler::find($teklif->id);
                $teklifGuncel->url = $pdf;
                $teklifGuncel->save();

                return response()->json([
                    'durum' => true,
                    'mesaj' => 'PDF başarıyla oluşturuldu.',
                    'url' => $pdf,
                ]);


                // return response()->json([
                //     'durum' => true,
                //     'mesaj' => 'Teklif başarılı bir şekilde eklendi.',
                //     'teklif' => $teklif->refresh(),
                // ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "F006",
            ], 500);
        }
    }
    public function teklifSil(Request $request)
    {
        DB::beginTransaction();

        try {
            $teklif = Teklifler::find($request->id);

            if (!$teklif->delete()) {
                DB::rollBack();

                return response()->json([
                    "durum" => false,
                    "mesaj" => "Teklif silinirken bir hata oluştu.",
                    "hataKodu" => "RS001",
                ], 500);
            }

            DB::commit();

            return response()->json([
                "durum" => true,
                "mesaj" => "Teklif silindi.",
            ], 200);
        } catch (\Exception $ex) {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Teklif silinirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
                "hataKodu" => "500",
            ], 500);
        }
    }
}
