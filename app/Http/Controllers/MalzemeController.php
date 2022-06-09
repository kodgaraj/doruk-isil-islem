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

            $malzeme = new Malzemeler();

            $malzeme->ad = $malzemeBilgileri['malzemeAdi'];
            $malzeme->birimFiyat = $malzemeBilgileri['malzemeBirimFiyat'] ?? 0;

            if (!$malzeme->save())
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Malzeme eklenemedi.',
                    "hataKodu" => "M005",
                ], 500);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Malzeme başarılı bir şekilde eklendi.',
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
