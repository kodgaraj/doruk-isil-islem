<?php

namespace App\Http\Controllers;

use App\Models\Kisitlar;
use App\Models\Roller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SistemController extends Controller
{
    public function index(){
        $kullanicilar = User::all();
        $roller = Roller::all();
        $kisitlar = Kisitlar::get()->first();

        if(!isset($kisitlar) && $kisitlar == null){
            $kisitlar = new Kisitlar();
            $kisitlar->save();
        }
        $kisitlar->kullanicilar = json_decode($kisitlar->kullanicilar);
        $kisitlar->roller = json_decode($kisitlar->roller);
        return view("kisitlamalar")->with(["kisitlar" => $kisitlar,"kullanicilar" => $kullanicilar, "roller" => $roller]);
    }
 /**
     * @global
     */

    public function kisitGuncelle(Request $request)
    {
        try {
            $saatBaslangic = $request->saatBaslangic;
            $saatBitis = $request->saatBitis;
            $ipler = $request->ipler;
            $kullanicilar = isset($request->kullanicilar) && $request->kullanicilar != null ? json_encode($request->kullanicilar) : [];
            $roller =  isset($request->roller) && $request->roller != null ? json_encode($request->roller) : [];

            $kisit = Kisitlar::get()->first();
            if(!isset($kisit) && $kisit == null){
                $kisit = new Kisitlar();
            }
            $kisit->saatBaslangic = $saatBaslangic;
            $kisit->saatBitis = $saatBitis;
            $kisit->ipler = $ipler;
            $kisit->kullanicilar = $kullanicilar;
            $kisit->roller = $roller;


            if (!$kisit->save()) {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Kısıtlama güncellenemedi.',
                    "hataKodu" => "F005",
                ], 500);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Kısıtlama başarılı bir şekilde güncellendi.',
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

}
