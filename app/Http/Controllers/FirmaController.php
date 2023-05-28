<?php

namespace App\Http\Controllers;

use App\Models\Firmalar;
use App\Models\Siparisler;
use App\Models\Teklifler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirmaController extends Controller
{
    public function index()
    {
        return view("firmalar", [
            "paraBirimleri" => array_values($this->paraBirimleri),
        ]);
    }

    public function firmaEkle(Request $request)
    {
        try {
            $firmaBilgileri = $request->firma;

            if (isset($firmaBilgileri["id"]) && $firmaBilgileri["id"])
            {
                $firma = Firmalar::find($firmaBilgileri["id"]);
            }
            else
            {
                $firma = new Firmalar();
            }

            $firma->firmaAdi = $this->buyukHarf($firmaBilgileri['firmaAdi']);
            $firma->sorumluKisi = isset($firmaBilgileri['sorumluKisi']) && $firmaBilgileri['sorumluKisi'] ? $this->buyukHarf($firmaBilgileri['sorumluKisi']) : null;
            $firma->telefon = isset($firmaBilgileri['telefon']) && $firmaBilgileri['telefon'] ? $firmaBilgileri['telefon'] : null;
            $firma->eposta = isset($firmaBilgileri['eposta']) && $firmaBilgileri['eposta'] ? $firmaBilgileri['eposta'] : null;
            $firma->adres = isset($firmaBilgileri['adres']) && $firmaBilgileri['adres'] ? $this->buyukHarf($firmaBilgileri['adres']) : null;

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

                foreach ($firmalar as $firma) {
                    $teklifSayisi = Teklifler::where('firmaId', $firma->id)->count();
                    $firma->teklifVarMi = ($teklifSayisi > 0) ? true : false;
                }
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

    public function firmalariBirlestir(Request $request)
    {
        try
        {
            $anaFirma = $request->anaFirma;
            $birlestirilecekFirma = $request->birlestirilecekFirma;

            $siparisSayisi = Siparisler::where("firmaId", $birlestirilecekFirma["id"])
                ->count();

            if ($siparisSayisi === 0)
            {
                $sonuc = Firmalar::where("id", $birlestirilecekFirma["id"])
                    ->delete();

                if (!$sonuc)
                {
                    return response()->json([
                        'durum' => false,
                        'mesaj' => 'Birleştirilecek firma silinemedi.',
                        "hataKodu" => "FB003",
                    ], 500);
                }

                return response()->json([
                    'durum' => true,
                    'mesaj' => 'Firmalar başarıyla birleştirildi.',
                ]);
            }

            $sonuc = Siparisler::where("firmaId", $birlestirilecekFirma["id"])
                ->update(["firmaId" => $anaFirma["id"]]);

            if (!$sonuc)
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Firma birleştirilemedi.',
                    "hataKodu" => "FB001",
                ], 500);
            }

            $sonuc = Firmalar::where("id", $birlestirilecekFirma["id"])
                ->delete();

            if (!$sonuc)
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Birleştirilecek firma silinemedi.',
                    "hataKodu" => "FB002",
                ], 500);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Firmalar başarıyla birleştirildi.',
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }
}
