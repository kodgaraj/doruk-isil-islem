<?php

namespace App\Http\Controllers;

use App\Models\Firmalar;
use App\Models\Teklifler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeklifController extends Controller
{
    public function index()
    {
        return view("teklifler");
    }
 /**
     * @global
     */
    public function teklifleriGetir(Request $request)
    {
        try {
            $filtreleme = isset($request->filtreleme) ? json_decode($request->filtreleme, true) : [];
            $sayfalama = $request->sayfalama ?? false;
            $sablonTabloAdi = (new Teklifler())->getTable();

            if (!$sayfalama) {
                $teklifler = Teklifler::select('teklifler.*', 'firmalar.firmaAdi', 'firmalar.eposta')
                    ->join('firmalar', 'firmalar.id', '=', 'teklifler.firmaId')
                    ->orderBy("created_at", "desc")->get();
            } else {
                if (isset($filtreleme["siralamaTuru"]) && $filtreleme["siralamaTuru"])
                {
                    $alan = array_keys($filtreleme["siralamaTuru"])[0];
                    $siralamaTuru = array_values($filtreleme["siralamaTuru"])[0];
                    $teklifler = Teklifler::orderBy($alan, $siralamaTuru);
                }
                else
                {
                    $teklifler = Teklifler::orderBy("created_at", "desc");
                }

                if (isset($filtreleme["arama"]) && $filtreleme["arama"] != "")
                {
                    $teklifler->where("$firmaTabloAdi.teklifAdi", "like", "%" . $filtreleme["arama"] . "%")
                        ->orWhere("$firmaTabloAdi.tur", "like", "%" . $filtreleme["arama"] . "%");
                }
                $teklifler = $teklifler->paginate(10);

            }

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
            $tur = $request->tur;
            $html = $request->html;
            $teklifBilgileri = $request->teklifBilgileri;

            $teklif = new Teklifler();
            $teklif->firmaId = $firmaId;
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
