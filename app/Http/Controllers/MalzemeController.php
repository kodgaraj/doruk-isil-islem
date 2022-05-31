<?php

namespace App\Http\Controllers;

use App\Models\Malzemeler;

class MalzemeController extends Controller
{
    public function malzemeleriGetir()
    {
        $malzemeler = Malzemeler::all();

        return response()->json([
            'durum' => true,
            'mesaj' => 'Malzemeler başarıyla getirildi.',
            'malzemeler' => $malzemeler
        ]);
    }
}
