<?php

namespace App\Http\Controllers;

use App\Models\Firinlar;
use App\Models\Islemler;
use App\Models\Siparisler;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $firinlar = Firinlar::all();

        $toplamKayitlar = [
            "siparisler" => 0,
            "kullanicilar" => 0,
            "islemler" => 0,
        ];

        $toplamKayitlar["siparisler"] = Siparisler::count();
        $toplamKayitlar["kullanicilar"] = User::count();
        $toplamKayitlar["islemler"] = Islemler::count();

        return view("index", [
            "firinlar" => $firinlar,
            "toplamKayitlar" => $toplamKayitlar,
        ]);
    }
}
