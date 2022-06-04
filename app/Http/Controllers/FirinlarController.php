<?php

namespace App\Http\Controllers;

use App\Models\Firinlar;

class FirinlarController extends Controller
{
    public function firinlariGetir()
    {
        $firinlar = Firinlar::all();

        return response()->json([
            'durum' => true,
            'mesaj' => 'Firinlar başarılı bir şekilde getirildi.',
            'firinlar' => $firinlar,
        ], 200);
    }
}
