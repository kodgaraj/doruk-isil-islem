<?php

namespace App\Http\Controllers;

use App\Models\IslemTurleri;
use Illuminate\Http\Request;

class IslemTurleriController extends Controller
{
    public function islemTurleriGetir()
    {
        $islemTurleri = IslemTurleri::orderBy("created_at", "desc")->get();

        return response()->json([
            'durum' => true,
            'mesaj' => 'İşlem türleri başarıyla getirildi.',
            'islemTurleri' => $islemTurleri
        ]);
    }

    public function islemTuruEkle(Request $request)
    {
        try
        {
            $islemTuruBilgileri = $request->islemTuru;

            if (!isset($islemTuruBilgileri['islemTuruAdi']) || !$islemTuruBilgileri['islemTuruAdi'])
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem türü adı boş olamaz.',
                    "hataKodu" => "IT001",
                ], 500);
            }

            $islemTuru = new IslemTurleri();

            $islemTuru->ad = $this->buyukHarf($islemTuruBilgileri['islemTuruAdi']);

            if (!$islemTuru->save())
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'İşlem türü eklenemedi.',
                    "hataKodu" => "IT005",
                ], 500);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'İşlem türü başarılı bir şekilde eklendi.',
                'islemTuru' => $islemTuru->refresh(),
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "IT006",
            ], 500);
        }
    }
}
