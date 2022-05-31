<?php

namespace App\Http\Controllers;

use App\Models\IslemTurleri;

class IslemTurleriController extends Controller
{
    public function islemTurleriGetir()
    {
        $islemTurleri = IslemTurleri::all();

        return response()->json([
            'durum' => true,
            'mesaj' => 'İşlem türleri başarıyla getirildi.',
            'islemTurleri' => $islemTurleri
        ]);
    }
}
