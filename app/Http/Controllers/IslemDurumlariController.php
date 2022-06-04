<?php

namespace App\Http\Controllers;

use App\Models\IslemDurumlari;

class IslemDurumlariController extends Controller
{
    public function islemDurumlariGetir()
    {
        $islemDurumlari = IslemDurumlari::all();

        return response()->json([
            'durum' => true,
            'mesaj' => 'İşlem türleri başarıyla getirildi.',
            'islemDurumlari' => $islemDurumlari
        ]);
    }
}
