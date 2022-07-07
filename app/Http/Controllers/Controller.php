<?php

namespace App\Http\Controllers;

use App\Models\Bildirimler;
use App\Models\BildirimTurleri;
use App\Models\Formlar;
use App\Models\IslemDurumlari;
use App\Models\Islemler;
use App\Models\OkunmamisBildirimler;
use App\Models\Siparisler;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;
use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function terminHesapla($tarih, $terminSuresi = 5)
    {
        $birinciFaz = floor($terminSuresi * 30 / 100);
        $ikinciFaz = floor($terminSuresi * 60 / 100);

        $islemTarih = Carbon::parse($tarih);
        $simdiTarih = Carbon::now();

        $termin = $islemTarih->diffInDays($simdiTarih);
        $renk = "success";

        if ($termin > $ikinciFaz)
        {
            $renk = "danger";
        }
        elseif ($termin > $birinciFaz)
        {
            $renk = "warning";
        }
        else
        {
            $renk = "success";
        }

        return [
            'gecenSure' => $termin,
            'gecenSureRenk' => $renk,
        ];
    }

    /**
     * İşleme ait formun içindeki tüm işlemler tamamlandıysa,
     * bu formun bitiş tarihini ayarlar.
     */
    public function islemBitisTarihleriAyarla($islemId)
    {
        try
        {
            $islemTabloAdi = (new Islemler())->getTable();
            $formTabloAdi = (new Formlar())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();

            // Formun bitiş tarihini ayarlama
            $form = Formlar::join($islemTabloAdi, $formTabloAdi . '.id', '=', $islemTabloAdi . '.formId')
                ->where("$islemTabloAdi.id", $islemId)
                ->first();

            if (!$form)
            {
                return false;
            }

            $tamamlanmamisFormIslemler = Islemler::join($islemDurumTabloAdi, $islemDurumTabloAdi . '.id', '=', $islemTabloAdi . '.durumId')
                ->where("$islemTabloAdi.formId", $form->formId)
                ->where("$islemDurumTabloAdi.kod", "<>", "TAMAMLANDI")
                ->count();

            $guncellenecekForm = Formlar::find($form->formId);
            if ($tamamlanmamisFormIslemler === 0)
            {
                $guncellenecekForm->bitisTarihi = Carbon::now();

                $this->bildirimAt(auth()->user()->id, [
                    "baslik" => "Isıl İşlem Formu Tamamlandı",
                    "icerik" => "$form->formId numaralı idye ait ısıl işlem formu tamamlandı.",
                    "link" => "/isil-islemler/$form->formId",
                    "kod" => "FORM_BILDIRIMI",
                    "actionId" => $form->formId,
                ]);
            }
            else
            {
                $guncellenecekForm->bitisTarihi = null;
            }

            if (!$guncellenecekForm->save())
            {
                return false;
            }

            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    /**
     * Yıl ve ay klasörleri altında dosya saklar
     *
     * @param string $base64 Dosya içeriği
     * @return boolean
     *
     * @example $this->base64ResimKaydet($base64, ["dosyaAdi" => "$siparisNo-$islemNo"]);
     */
    public function base64ResimKaydet($base64, $parametreler = [])
    {
        if ($base64) {
            $yil = Carbon::now()->year;
            $ay = Carbon::now()->month;
            $klasorYolu = "uploads/$yil/$ay/";
            $dosyaAdi = "";

            if (isset($parametreler["altKlasor"]) && count($parametreler["altKlasor"]) > 0)
            {
                $klasorYolu .= implode("/", $parametreler["altKlasor"]) . "/";
            }

            if (!file_exists($klasorYolu))
            {
                mkdir(
                    directory: $klasorYolu,
                    recursive: true
                );
            }

            if (isset($parametreler["dosyaAdi"]) && $parametreler["dosyaAdi"])
            {
                $dosyaAdi = $parametreler["dosyaAdi"];
            }
            else
            {
                $dosyaAdi = uniqid();
            }

            $base64Image = explode(";base64,", $base64);
            $explodeImage = explode("image/", $base64Image[0]);
            $resimUzanti = $explodeImage[1];
            $resimBase64 = base64_decode($base64Image[1]);
            $dosyaYolu = $klasorYolu . $dosyaAdi . '.' . $resimUzanti;
            $publicDosyaYolu = public_path($dosyaYolu);
            $appDosyaYolu = app_path($dosyaYolu);

            // dd([
            //     "dosyaYolu" => $dosyaYolu,
            //     "publicDosyaYolu" => $publicDosyaYolu,
            //     "appDosyaYolu" => $appDosyaYolu,
            // ]);

            return !!file_put_contents($dosyaYolu, $resimBase64)
                ? $dosyaYolu . '?v=' . time()
                : false;
        }

        return false;
    }

    /**
     * Dosyayı yoluna göre siler
     *
     * @param string $dosyaYolu Dosya yolu
     */
    public function dosyaSil($dosyaYolu)
    {
        // versiyon bilgisini siler
        $dosyaYolu = preg_replace('/\?v=[0-9]+/', '', $dosyaYolu);

        if (file_exists($dosyaYolu))
        {
            return unlink($dosyaYolu);
        }

        return true;
    }

    public function buyukHarf($degisken)
    {
        if (!$degisken)
        {
            return $degisken;
        }

        $degisken = str_replace(["i", "ı"], ['İ', "I"], $degisken);

        return mb_strtoupper($degisken);
    }

    public function kucukHarf($degisken)
    {
        if (!$degisken)
        {
            return $degisken;
        }

        $degisken = str_replace(["I", "İ"], ['i', "ı"], $degisken);

        return mb_strtolower($degisken);
    }

    public function bildirimAt($kullaniciId, $veriler)
    {
        try
        {
            $btid = BildirimTurleri::where("kod", $veriler["kod"])->first()->id;
            $kullaniciId = $kullaniciId ?? Auth::user()->id;
            $baslik = $veriler["baslik"];
            $icerik = $veriler["icerik"];
            $link = $veriler["link"];
            $kod = $veriler["kod"];
            $actionId = $veriler["actionId"] ?? null;

            $bildirim = new Bildirimler();
            $bildirim->btId = $btid;
            $bildirim->kullaniciId = $kullaniciId;
            $bildirim->baslik = $baslik;
            $bildirim->icerik = $icerik;
            $bildirim->json = json_encode([
                "link" => $link,
                "kod" => $kod,
                "actionId" => $actionId
            ]);

            if (!$bildirim->save())
            {
                return [
                    "durum" => false,
                    "mesaj" => "Bildirim kaydedilemedi."
                ];
            }

            $mesajlar = [
                new ExpoMessage([
                    "title" => $baslik,
                    "body" => $icerik,
                    "data" => [
                        "link" => $link,
                        "kod" => $kod,
                        "actionId" => $actionId,
                        "bildirimId" => $bildirim->id,
                    ],
                ])
            ];

            $pushTokens = [];

            $kullanicilar = User::where("id", "<>", $kullaniciId)->get();

            foreach ($kullanicilar as $kullanici)
            {
                $bildirimKullanicilar = new OkunmamisBildirimler();
                $bildirimKullanicilar->bildirimId = $bildirim->id;
                $bildirimKullanicilar->kullaniciId = $kullanici->id;

                if (!$bildirimKullanicilar->save())
                {
                    return [
                        "durum" => false,
                        "mesaj" => "Okunmamış bildirim kaydedilemedi.",
                    ];
                }

                $pushTokens[] = $kullanici->pushToken;
            }

            (new Expo())->send($mesajlar)->to($pushTokens)->push();

            return true;
        }
        catch (\Exception $e)
        {
            return [
                "durum" => false,
                "mesaj" => $e->getMessage(),
            ];
        }
    }

    public function bildirimOku($bildirimIdleri, $kullaniciId = null)
    {
        try
        {
            $kullaniciId = $kullaniciId ?: Auth::user()->id;

            $bildirimIdleri = is_array($bildirimIdleri) ? $bildirimIdleri : [$bildirimIdleri];
            $bildirimIdleri = array_filter($bildirimIdleri);
            $bildirimIdleri = array_unique($bildirimIdleri);
            $bildirimIdleri = array_values($bildirimIdleri);

            $bildirimKullanicilar = OkunmamisBildirimler::whereIn("bildirimId", $bildirimIdleri)
                ->where("kullaniciId", $kullaniciId)
                ->get();

            foreach ($bildirimKullanicilar as $bildirimKullanici)
            {
                $bildirimKullanici->delete();
            }

            return true;
        }
        catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
            return false;
        }
    }

    public function bildirimGetir($kullaniciId = null, $veriler = [])
    {
        try
        {
            $limit = $veriler["sayfalama"] ?? 20;
            $okuma = $veriler["okuma"] ?? false;
            $kullaniciId = $kullaniciId ?: Auth::user()->id;
            $filtreleme = $veriler["filtreleme"] ?? [];

            $okunmamisBildirimTabloAdi = (new OkunmamisBildirimler())->getTable();
            $bildirimTabloAdi = (new Bildirimler())->getTable();
            $bildirimTuruTabloAdi = (new BildirimTurleri())->getTable();

            $bildirimler = Bildirimler::selectRaw("
                $bildirimTabloAdi.*,
                $bildirimTuruTabloAdi.ad as bildirimTuruAdi,
                $bildirimTuruTabloAdi.kod as bildirimTuruKodu,
                $bildirimTuruTabloAdi.json as bildirimTuruJson,
                IF($okunmamisBildirimTabloAdi.bildirimId IS NULL, 1, 0) as okundu
            ")
            ->leftJoin($okunmamisBildirimTabloAdi, function ($join) use ($kullaniciId, $bildirimTabloAdi, $okunmamisBildirimTabloAdi) {
                $join->on($okunmamisBildirimTabloAdi . ".bildirimId", "=", $bildirimTabloAdi . ".id");
                $join->on($okunmamisBildirimTabloAdi . ".kullaniciId", "=", DB::raw($kullaniciId));
            })
            ->join($bildirimTuruTabloAdi, "$bildirimTuruTabloAdi.id", "=", $bildirimTabloAdi . ".btid")
            ->orderBy("id", "desc");

            if (isset($filtreleme["arama"]) && $filtreleme["arama"] != "")
            {
                $bildirimler->where("$bildirimTabloAdi.baslik", "like", "%" . $filtreleme["arama"] . "%")
                    ->orWhere("$bildirimTabloAdi.icerik", "like", "%" . $filtreleme["arama"] . "%")
                    ->orWhere("$bildirimTuruTabloAdi.ad", "like", "%" . $filtreleme["arama"] . "%");
            }

            $bildirimler = $bildirimler->paginate($limit)->toArray();

            foreach ($bildirimler["data"] as &$bildirim)
            {
                $bildirim["json"] = json_decode($bildirim["json"]);
                $bildirim["bildirimTuruJson"] = json_decode($bildirim["bildirimTuruJson"]);
                $bildirim["okundu"] = $bildirim["okundu"] == 1;
            }

            // Eğer "okuma" gönderilmediyse dönecek bildirimleri okundu olarak işaretler
            if (!$okuma)
            {
                $bildirimIdleri = array_column($bildirimler["data"], "id");

                if (!$this->bildirimOku($bildirimIdleri, $kullaniciId))
                {
                    return [
                        "durum" => false,
                        "mesaj" => "Bildirimler okunamadı.",
                    ];
                }
            }

            // Toplam okunmamış bildirim sayısını bulur
            $toplamOkunmamisSayisi = $this->toplamOkunmamisBildirimSayisi($kullaniciId);

            return [
                "veriler" => $bildirimler,
                "toplamOkunmamisSayisi" => $toplamOkunmamisSayisi,
                "durum" => true,
            ];
        }
        catch (\Exception $e)
        {
            return [
                "durum" => false,
                "mesaj" => $e->getMessage(),
                "satir" => $e->getLine(),
            ];
        }
    }

    public function toplamOkunmamisBildirimSayisi($kullaniciId = null)
    {
        $kullaniciId = $kullaniciId ?: Auth::user()->id;
        return OkunmamisBildirimler::where("kullaniciId", $kullaniciId)->count();
    }
}
