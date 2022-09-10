<?php

namespace App\Http\Controllers;

use App\Models\Malzemeler;
use Illuminate\Http\Request;

class MalzemeController extends Controller
{
    public function malzemeleriGetir()
    {
        $malzemeler = Malzemeler::orderBy("created_at", "desc")->get();

        return response()->json([
            'durum' => true,
            'mesaj' => 'Malzemeler başarıyla getirildi.',
            'malzemeler' => $malzemeler
        ]);
    }

    public function malzemeEkle(Request $request)
    {
        try
        {
            $malzemeBilgileri = $request->malzeme;

            if (!isset($malzemeBilgileri['malzemeAdi']) || !$malzemeBilgileri['malzemeAdi'])
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Malzeme adı boş olamaz.',
                    "hataKodu" => "M001",
                ], 500);
            }

            if (isset($malzemeBilgileri["malzemeId"]) && $malzemeBilgileri["malzemeId"])
            {
                $malzeme = Malzemeler::find($malzemeBilgileri["malzemeId"]);
            }
            else
            {
                $malzeme = new Malzemeler();
            }

            $malzeme->ad = $this->buyukHarf($malzemeBilgileri['malzemeAdi']);
            $malzeme->birimFiyat = $malzemeBilgileri['malzemeBirimFiyat'] ?? 0;

            if (!$malzeme->save())
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Malzeme kaydedilemedi.',
                    "hataKodu" => "M005",
                ], 500);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Malzeme başarılı bir şekilde kaydedildi.',
                'malzeme' => $malzeme->refresh(),
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "M006",
            ], 500);
        }
    }
}
