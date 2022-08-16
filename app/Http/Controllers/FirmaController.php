<?php

namespace App\Http\Controllers;

use App\Models\Firmalar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirmaController extends Controller
{
    public function index()
    {
        return view("firmalar");
    }

    public function firmaEkle(Request $request)
    {
        try {
            $firmaBilgileri = $request->firma;

            $firma = new Firmalar();

            $firma->firmaAdi = $this->buyukHarf($firmaBilgileri['firmaAdi']);
            $firma->sorumluKisi = $this->buyukHarf($firmaBilgileri['sorumluKisi']) ?: null;
            $firma->telefon = $firmaBilgileri['telefon'];

            if (!$firma->save()) {
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
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "F006",
            ], 500);
        }
    }

    public function firmaSil(Request $request)
    {
        DB::beginTransaction();

        try {
            $firma = Firmalar::find($request->id);

            if (!$firma->delete()) {
                DB::rollBack();

                return response()->json([
                    "durum" => false,
                    "mesaj" => "Firma silinirken bir hata oluştu.",
                    "hataKodu" => "RS001",
                ], 500);
            }

            DB::commit();

            return response()->json([
                "durum" => true,
                "mesaj" => "Firma silindi.",
            ], 200);
        } catch (\Exception $ex) {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Firma silinirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
                "hataKodu" => "500",
            ], 500);
        }
    }

    /**
     * @global
     */
    public function firmalariGetir(Request $request)
    {
        try {
            $filtreleme = isset($request->filtreleme) ? json_decode($request->filtreleme, true) : [];
            $sayfalama = $request->sayfalama ?? false;
            $firmaTabloAdi = (new Firmalar())->getTable();

            if (!$sayfalama) {
                $firmalar = Firmalar::orderBy("created_at", "desc")->get();
            } else {
                if (isset($filtreleme["siralamaTuru"]) && $filtreleme["siralamaTuru"])
                {
                    $alan = array_keys($filtreleme["siralamaTuru"])[0];
                    $siralamaTuru = array_values($filtreleme["siralamaTuru"])[0];
                    $firmalar = Firmalar::orderBy($alan, $siralamaTuru);
                }
                else
                {
                    $firmalar = Firmalar::orderBy("created_at", "desc");
                }

                if (isset($filtreleme["arama"]) && $filtreleme["arama"] != "")
                {
                    $firmalar->where("$firmaTabloAdi.firmaAdi", "like", "%" . $filtreleme["arama"] . "%")
                        ->orWhere("$firmaTabloAdi.sorumluKisi", "like", "%" . $filtreleme["arama"] . "%")
                        ->orWhere("$firmaTabloAdi.telefon", "like", "%" . $filtreleme["arama"] . "%");
                }

                $firmalar = $firmalar->paginate(10);
            }


            return response()->json([
                'durum' => true,
                'mesaj' => 'Firmalar başarıyla getirildi.',
                'firmalar' => $firmalar
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }
}
