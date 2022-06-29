<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogKayitlariController extends Controller
{
    protected $islemler = [
        "created" => ["ad" => "Ekleme", "kod" => "created", "renk" => "success"],
        "updated" => ["ad" => "Güncelleme", "kod" => "updated", "renk" => "warning"],
        "deleted" => ["ad" => "Silme", "kod" => "deleted", "renk" => "danger"],
    ];

    public function index()
    {
        $kullanicilar = User::all();

        return view("log-kayitlari", [
            "kullanicilar" => $kullanicilar,
            "islemler" => array_values($this->islemler),
        ]);
    }

    public function logKayitlariGetir(Request $request)
    {
        try
        {
            $filtreleme = json_decode($request->filtreleme, true);

            $kullaniciTabloAdi = (new User())->getTable();
            $logTabloAdi = (new Activity())->getTable();

            $logKayitlari = Activity::select("$logTabloAdi.*", "$kullaniciTabloAdi.name as kullaniciAdi")
                ->join($kullaniciTabloAdi, $kullaniciTabloAdi . ".id", "=", $logTabloAdi . ".causer_id")
                ->orderBy("$logTabloAdi.created_at", "desc");

            if (isset($filtreleme["arama"]) && $filtreleme["arama"] != "")
            {
                $logKayitlari->where("$logTabloAdi.description", "like", "%" . $filtreleme["arama"] . "%")
                    ->orWhere("$logTabloAdi.subject_id", "like", "%" . $filtreleme["arama"] . "%")
                    ->orWhere("$logTabloAdi.causer_id", "like", "%" . $filtreleme["arama"] . "%");
            }

            if (isset($filtreleme["kullanicilar"]) && count($filtreleme["kullanicilar"]) > 0)
            {
                $logKayitlari->whereIn("$kullaniciTabloAdi.id", array_column($filtreleme["kullanicilar"], "id"));
            }

            if (isset($filtreleme["islemler"]) && count($filtreleme["islemler"]) > 0)
            {
                $logKayitlari->whereIn("$logTabloAdi.event", array_column($filtreleme["islemler"], "kod"));
            }

            if (isset($filtreleme["baslangicTarihi"]) && $filtreleme["baslangicTarihi"] != "")
            {
                $logKayitlari->where("$logTabloAdi.created_at", ">=", $filtreleme["baslangicTarihi"]);

                if (isset($filtreleme["bitisTarihi"]) && $filtreleme["bitisTarihi"] != "")
                {
                    $logKayitlari->where("$logTabloAdi.created_at", "<=", $filtreleme["bitisTarihi"]);
                }
            }
            else if (isset($filtreleme["bitisTarihi"]) && $filtreleme["bitisTarihi"] != "")
            {
                $logKayitlari->where("$logTabloAdi.created_at", "<=", $filtreleme["bitisTarihi"]);
            }

            $logKayitlari = $logKayitlari->paginate(10);

            foreach ($logKayitlari as &$logKayit)
            {
                $logKayit->olay = $this->islemler[$logKayit->event];
                // $logKayit->mesaj = "$logKayit->subject_id numaralı " . $this->kucukHarf($logKayit->description);
            }

            return response()->json([
                "durum" => true,
                "mesaj" => "Log kayıtları getirildi",
                "logKayitlari" => $logKayitlari,
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                "durum" => false,
                "mesaj" => "Log kayıtları getirilemedi",
                "hata" => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "LK_CATCH",
            ]);
        }
    }
}
