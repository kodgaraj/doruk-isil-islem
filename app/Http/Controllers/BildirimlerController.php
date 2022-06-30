<?php

namespace App\Http\Controllers;

use App\Models\BildirimTurleri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BildirimlerController extends Controller
{
    public function index()
    {
        return view("bildirimler");
    }

    public function bildirimleriGetir(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $bildirimleriOkuma = $request->okuma ?? false;
            $sayfalamaAdeti = $request->sayfalama ?? 20;
            $kullaniciId = $request->kullaniciId ?? auth()->user()->id;
            $filtreleme = json_decode($request->filtreleme ?? null, true);

            $bildirimler = $this->bildirimGetir($kullaniciId, [
                "okuma" => $bildirimleriOkuma,
                "sayfalama" => $sayfalamaAdeti,
                "filtreleme" => $filtreleme,
            ]);

            if (!$bildirimler["durum"])
            {
                DB::rollBack();

                return response()->json([
                    'durum' => false,
                    "mesaj" => "Bildirimler getirilirken bir hata oluştu.",
                    'bildirimler' => $bildirimler,
                ], 400);
            }

            unset($bildirimler['durum']);

            DB::commit();

            return response()->json([
                'durum' => true,
                "mesaj" => "Bildirimler başarılı bir şekilde getirildi.",
                'bildirimler' => $bildirimler,
            ], 200);
        }
        catch (\Exception $ex)
        {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Bildirimler getirilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
                "hataKodu" => "500",
            ], 500);
        }
    }

    public function okunmamisBildirimSayisiGetir(Request $request)
    {
        $kullaniciId = $request->kullaniciId ?? auth()->user()->id;

        $okunmamisBildirimSayisi = $this->toplamOkunmamisBildirimSayisi($kullaniciId);

        return response()->json([
            'durum' => true,
            "mesaj" => "Okunmamış bildirim sayısı başarılı bir şekilde getirildi.",
            'okunmamisBildirimSayisi' => $okunmamisBildirimSayisi,
        ], 200);
    }

    public function miniBildirimleriGetir(Request $request)
    {
        $kullaniciId = $request->kullaniciId ?? auth()->user()->id;

        $bildirimler = $this->bildirimGetir($kullaniciId, ["okuma" => true]);

        if (!$bildirimler)
        {
            return response()->json([
                'durum' => false,
                "mesaj" => "Bildirimler başarılı bir şekilde getirildi.",
                'bildirimler' => $bildirimler,
            ], 400);
        }

        unset($bildirimler['durum']);

        return response()->json([
            'durum' => true,
            "mesaj" => "Bildirimler başarılı bir şekilde getirildi.",
            'bildirimler' => $bildirimler,
        ], 200);
    }
}
