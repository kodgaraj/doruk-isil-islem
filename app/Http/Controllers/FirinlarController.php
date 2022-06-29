<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use App\Models\Firinlar;
use App\Models\Islemler;

class FirinlarController extends Controller 
{   
    public function index()
    { 
        $renkler = [
            [

                "ad" => "Yeşil",
                "kod" => "success",
            ],
            [
                "ad" => "Kırmızı",
                "kod" => "danger",
            ],
            [
                "ad" => "Mavi",
                "kod" => "primary",
            ],
            [
                "ad" => "Turkuaz",
                "kod" => "info",
            ],
            [
                "ad" => "Sarı",
                "kod" => "warning",
            ],
            [
                "ad" => "Gri",
                "kod" => "secondary",
            ],
            [
                "ad" => "Siyah",
                "kod" => "dark",
            ],
        ];

        return view("firinlar", [
            "renkler" => $renkler,
        ]);

        
    }

    public function firinlariGetir()
    {   
        $islemTabloAdi = (new Islemler())->getTable();
        $firinTabloAdi = (new Firinlar())->getTable();

        $firinlar = Firinlar::join("$islemTabloAdi", "$firinTabloAdi.id", "=", "$islemTabloAdi.firinId")
        ->selectRaw("$firinTabloAdi.id, $firinTabloAdi.ad, $firinTabloAdi.kod, $firinTabloAdi.json, COUNT($islemTabloAdi.id) as islemSayisi")
        ->groupBy("$firinTabloAdi.id","$firinTabloAdi.ad","$firinTabloAdi.kod","$firinTabloAdi.json")
        ->get();

        foreach ($firinlar as $firin)
        {
            $firin->json=json_decode($firin->json);
        }

        return response()->json([
            'durum' => true,
            'mesaj' => 'Firinlar başarılı bir şekilde getirildi.',
            'firinlar' => $firinlar,
        ], 200);
    }

    public function firinKaydet(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $firinBilgileri = $request->firin;

            if (isset($firinBilgileri["id"]))
            {
                $firin = Firinlar::find($firinBilgileri["id"]);
            }
            else
            {
                $firin = new Firinlar();
            }

            $firin->ad = $this->buyukHarf($firinBilgileri['ad']);
            $firin->kod = $firinBilgileri['kod'];
            $firin->json = json_encode($firinBilgileri['json']);

            if (!$firin->save())
            {
                DB::rollBack();

                return response()->json([
                    "durum" => false,
                    "mesaj" => "Rol kaydedilirken bir hata oluştu.",
                    "hataKodu" => "RK001",
                ], 500);
            }

            DB::commit();

            return response()->json([
                "durum" => true,
                "mesaj" => "Fırın kaydedildi.",
                "firin" => $firin,
            ], 200);
        }
        catch (\Exception $ex)
        {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Fırın kaydedilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
                "hataKodu" => "500",
            ], 500);
        }
    }

    public function firinSil(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $firin = Firinlar::find($request->id);

            if (!$firin->delete())
            {
                DB::rollBack();

                return response()->json([
                    "durum" => false,
                    "mesaj" => "Fırın silinirken bir hata oluştu.",
                    "hataKodu" => "RS001",
                ], 500);
            }

            DB::commit();

            return response()->json([
                "durum" => true,
                "mesaj" => "Fırın silindi.",
            ], 200);
        }
        catch (\Exception $ex)
        {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Fırın silinirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
                "hataKodu" => "500",
            ], 500);
        }
    }
}
