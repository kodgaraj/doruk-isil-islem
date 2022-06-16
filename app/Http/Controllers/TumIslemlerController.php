<?php

namespace App\Http\Controllers;

use App\Models\Firinlar;
use App\Models\IslemDurumlari;

class TumIslemlerController extends Controller
{
    public function index()
    {
        $firinlar = Firinlar::all();
        $islemDurumlari = IslemDurumlari::all();

        return view('tum-islemler', [
            'firinlar' => $firinlar,
            'islemDurumlari' => $islemDurumlari,
        ]);
    }
}
