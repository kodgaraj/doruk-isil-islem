<?php

namespace App\Http\Controllers;

use App\Models\Sablonlar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SablonController extends Controller
{

    public function index()
    {
        return view("sablonlar");
    }
 /**
     * @global
     */
    public function sablonlariGetir(Request $request)
    {
        try {
            $filtreleme = isset($request->filtreleme) ? json_decode($request->filtreleme, true) : [];
            $tur = isset($request->tur) ? $request->tur : null;
            $sayfalama = $request->sayfalama ?? false;
            $sablonTabloAdi = (new Sablonlar())->getTable();

            if (!$sayfalama) {
                $sablonlar = Sablonlar::orderBy("created_at", "asc");
                if (isset($tur) && $tur != "")
                {
                    $sablonlar = $sablonlar->where("tur", $tur);
                }
                $sablonlar = $sablonlar->get();
            } else {
                if (isset($filtreleme["siralamaTuru"]) && $filtreleme["siralamaTuru"])
                {
                    $alan = array_keys($filtreleme["siralamaTuru"])[0];
                    $siralamaTuru = array_values($filtreleme["siralamaTuru"])[0];
                    $sablonlar = Sablonlar::orderBy($alan, $siralamaTuru);
                }
                else
                {
                    $sablonlar = Sablonlar::orderBy("created_at", "asc");
                }

                if (isset($filtreleme["arama"]) && $filtreleme["arama"] != "")
                {
                    $sablonlar->where("$sablonTabloAdi.sablonAdi", "like", "%" . $filtreleme["arama"] . "%")
                        ->orWhere("$sablonTabloAdi.tur", "like", "%" . $filtreleme["arama"] . "%");
                }

                if (isset($tur) && $tur != "")
                {
                    $sablonlar = $sablonlar->where("tur", $tur);
                }
                $sablonlar = $sablonlar->paginate(10);
            }

            foreach ($sablonlar as $key => $sablon)
            {
                $sablonlar[$key]->icerik = $sablon->icerik ? json_decode($sablon->icerik, true) : [];
                $sablonlar[$key]->icerik_html = $sablon->icerik_html ? json_decode($sablon->icerik_html, true) : [];
                $sablonlar[$key]->kullanilabilirOgeler = $sablon->kullanilabilirOgeler ? json_decode($sablon->kullanilabilirOgeler, true) : [];
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Şablonlar başarıyla getirildi.',
                'sablonlar' => $sablonlar
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }
    public function sablonEkle(Request $request)
    {
        try {
            $sablonBilgileri = $request->sablon;

            if (isset($sablonBilgileri["id"]) && $sablonBilgileri["id"])
            {
                $sablon = Sablonlar::find($sablonBilgileri["id"]);
            }
            else
            {
                $sablon = new Sablonlar();
            }
            // dd($sablonBilgileri);
            $sablon->sablonAdi = $this->buyukHarf($sablonBilgileri['sablonAdi']);
            $sablon->tur = isset($sablonBilgileri['tur']) && $sablonBilgileri['tur'] ? $this->buyukHarf($sablonBilgileri['tur']) : "TEKLIF";
            $sablon->icerik = isset($sablonBilgileri['icerik']) && $sablonBilgileri['icerik'] ? $sablonBilgileri['icerik'] : null;
            $sablon->icerik_html = isset($sablonBilgileri['icerik_html']) && $sablonBilgileri['icerik_html'] ? $sablonBilgileri['icerik_html'] : null;
            $sablon->kullanilabilirOgeler = isset($sablonBilgileri['kullanilabilirOgeler']) && $sablonBilgileri['kullanilabilirOgeler'] ? $sablonBilgileri['kullanilabilirOgeler'] : null;

            if (!$sablon->save()) {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Şablon eklenemedi.',
                    "hataKodu" => "F005",
                ], 500);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Şablon başarılı bir şekilde eklendi.',
                'firma' => $sablon->refresh(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "F006",
            ], 500);
        }
    }

    public function sablonSil(Request $request)
    {
        DB::beginTransaction();

        try {
            $sablon = Sablonlar::find($request->id);

            if (!$sablon->delete()) {
                DB::rollBack();

                return response()->json([
                    "durum" => false,
                    "mesaj" => "Şablon silinirken bir hata oluştu.",
                    "hataKodu" => "RS001",
                ], 500);
            }

            DB::commit();

            return response()->json([
                "durum" => true,
                "mesaj" => "Şablon silindi.",
            ], 200);
        } catch (\Exception $ex) {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Şablon silinirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
                "hataKodu" => "500",
            ], 500);
        }
    }
}
