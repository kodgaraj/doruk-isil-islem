<?php

namespace App\Http\Controllers;

use App\Models\Firmalar;
use Illuminate\Http\Request;

class FirmaController extends Controller
{
    public function firmaEkle(Request $request)
    {
        try
        {
            $firmaBilgileri = $request->firma;

            $firma = new Firmalar();

            $firma->firmaAdi = $this->buyukHarf($firmaBilgileri['firmaAdi']);
            $firma->sorumluKisi = $this->buyukHarf($firmaBilgileri['sorumluKisi']) ?: null;
            $firma->telefon = $firmaBilgileri['telefon'];

            if (!$firma->save())
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Firma eklenemedi.',
                    "hataKodu" => "F005",
                ], 500);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Firma başarılı bir şekilde eklendi.',
                'firma' => $firma->refresh(),
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "F006",
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
            $firmalar = Firmalar::orderBy("created_at", "desc")->get();

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
}
