<?php

namespace App\Http\Controllers;

use App\Models\Bildirimler;
use App\Models\BildirimTurleri;
use App\Models\Firmalar;
use App\Models\Formlar;
use App\Models\IslemDurumlari;
use App\Models\Islemler;
use App\Models\OkunmamisBildirimler;
use App\Models\SiparisDurumlari;
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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\ConsoleOutput;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $paraBirimleri = [
        "TL" => [
            "kod" => "TL",
            "sembol" => "₺",
            "ad" => "TL (₺)",
            "maske" => "tl",
        ],
        "USD" => [
            "kod" => "USD",
            "sembol" => "$",
            "ad" => "USD ($)",
            "maske" => "usd",
        ],
        "EURO" => [
            "kod" => "EURO",
            "sembol" => "€",
            "ad" => "EURO (€)",
            "maske" => "euro",
        ],
    ];

    public function terminHesapla($tarih, $terminSuresi = 5, $sonTarih = null)
    {
        $birinciFaz = floor($terminSuresi * 30 / 100);
        $ikinciFaz = floor($terminSuresi * 60 / 100);

        $islemTarih = Carbon::parse($tarih);
        $simdiTarih = $sonTarih ? Carbon::parse($sonTarih) : Carbon::now();

        $termin = $islemTarih->diffInDays($simdiTarih);
        $renk = "success";
        $kod = "TEMIZ";

        if ($termin > $ikinciFaz)
        {
            if ($sonTarih)
            {
               $renk = "white";
            }else{
                $renk = "danger";
            }

            $kod = "IKINCI_FAZ_GECIKMIS";
        }
        else if ($termin > $birinciFaz)
        {
            $renk = "warning";
            $kod = "BIRINCI_FAZ_GECIKMIS";
        }

        return [
            'gecenSure' => $termin,
            'gecenSureRenk' => $renk,
            'gecenSureKod' => $kod,
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
            $firmaTabloAdi = (new Firmalar())->getTable();

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
                    "icerik" => "$form->formId numaralı idye ait ısıl işlem formu tamamlandı. (Form Takip No: $form->takipNo)",
                    "link" => "/isil-islemler?formId=$form->formId",
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

            $siparis = Siparisler::join($islemTabloAdi, $siparisTabloAdi . '.id', '=', $islemTabloAdi . '.siparisId')
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->where("$islemTabloAdi.id", $islemId)
                ->first();

            if (!$siparis)
            {
                return false;
            }

            $tamamlanmamisSiparisIslemler = Islemler::join($islemDurumTabloAdi, $islemDurumTabloAdi . '.id', '=', $islemTabloAdi . '.durumId')
                ->where("$islemTabloAdi.siparisId", $siparis->siparisId)
                ->where("$islemDurumTabloAdi.kod", "<>", "TAMAMLANDI")
                ->count();

            if ($tamamlanmamisSiparisIslemler === 0)
            {
                $guncellenecekSiparis = Siparisler::find($siparis->siparisId);
                // Burada tekrar aynı işlemin yapılmasının sebebi, işlem tekrar ederse tamamlanan sipariş olursa diye.
                $siparisTamamlandiDurum = SiparisDurumlari::where("kod", "TAMAMLANDI")->first();

                $guncellenecekSiparis->durumId = $siparisTamamlandiDurum->id;
                $guncellenecekSiparis->bitisTarihi = Carbon::now();

                if (!$guncellenecekSiparis->save())
                {
                    return false;
                }

                $turkceTarih = Carbon::parse($siparis->tarih)->format('d.m.Y');

                $this->bildirimAt(auth()->user()->id, [
                    "baslik" => "Sipariş Formu Tamamlandı",
                    "icerik" => "$siparis->firmaAdi firmasının, $turkceTarih tarihli $siparis->siparisId numaralı sipariş formu tamamlandı. (Sipariş No: $siparis->siparisNo)",
                    "link" => "/siparis-formu?siparisId=$siparis->siparisId",
                    "kod" => "SIPARIS_BILDIRIMI",
                    "actionId" => $siparis->siparisId,
                ]);
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

    /**
     * Bildirim atma
     *
     * @param integer $kullaniciId Kullanıcı id
     * @param array $veriler Bildirim bilgileri
     *
     * @example $this->bildirimAt(1, [
     *    "baslik" => "Bildirim Başlığı",
     *    "icerik" => "Bildirim içeriği",
     *    "link" => "/link/adresi",
     *    "kod" => "KOD",
     *    "actionId" => 1 //?,
     * ]);
     */
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

    public function floatDonustur($deger, $parametreler = [])
    {
        $arr = explode(".", $deger);
        $binliksizPara = implode("", $arr);
        $sayi = str_replace(",", ".", $binliksizPara);

        if (isset($parametreler["paraBirimi"]))
        {
            $sayi = str_replace($parametreler["paraBirimi"]["sembol"], "", $sayi);
        }
        else if (isset($parametreler["kg"]))
        {
            $sayi = str_replace("kg", "", $sayi);
        }

        return round($sayi, 2);
    }

    public function yaziyaDonustur($deger, $parametreler = [])
    {
        $stringDeger = (string) $deger;
        $arr = explode(".", $stringDeger);
        $sayi = $arr[0];
        if (isset($arr[1]) && $arr[1])
        {
            $sayi .= "," . str_pad($arr[1], 2, "0");
        }
        else
        {
            $sayi .= ",00";
        }

        if (isset($parametreler["paraBirimi"]))
        {
            $sayi = $sayi . " " . $parametreler["paraBirimi"]["sembol"];
        }
        else if (isset($parametreler["kg"]))
        {
            $sayi = $sayi . " kg";
        }

        return $sayi;
    }

    public function siparisDurumKontrol($siparisId)
    {
        try
        {
            $islemTabloAdi = (new Islemler())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();

            $siparisIslemleri = Islemler::select("$islemTabloAdi.*", "$islemDurumTabloAdi.kod as islemDurumKodu")
                ->join($islemDurumTabloAdi, "$islemDurumTabloAdi.id", "=", "$islemTabloAdi.durumId")
                ->where("$islemTabloAdi.siparisId", $siparisId)
                ->get()
                ->toArray();

            $siparis = Siparisler::find($siparisId);

            // Sipariş işlemlerinin hepsi tamamlandığında siparişi tamamlandı olarak işaretle
            $durumlar = array_count_values(array_column($siparisIslemleri, "islemDurumKodu"));

            if (isset($durumlar["TAMAMLANDI"]) && $durumlar["TAMAMLANDI"] === count($siparisIslemleri))
            {
                $siparis->durumId = SiparisDurumlari::where("kod", "TAMAMLANDI")->first()->id;
                $siparis->bitisTarihi = Carbon::now();
            }
            else if (isset($durumlar["ISLEMDE"]) && $durumlar["ISLEMDE"] > 0)
            {
                $siparis->durumId = SiparisDurumlari::where("kod", "ISLEMDE")->first()->id;
                $siparis->bitisTarihi = null;
            }
            else
            {
                $siparis->durumId = SiparisDurumlari::where("kod", "SIPARIS_ALINDI")->first()->id;
                $siparis->bitisTarihi = null;
            }

            $siparis->save();

            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    public function formDurumKontrol($formId)
    {
        try
        {
            $islemTabloAdi = (new Islemler())->getTable();
            $islemDurumTabloAdi = (new IslemDurumlari())->getTable();

            $formIslemleri = Islemler::select("$islemTabloAdi.*", "$islemDurumTabloAdi.kod as islemDurumKodu")
                ->join($islemDurumTabloAdi, "$islemDurumTabloAdi.id", "=", "$islemTabloAdi.durumId")
                ->where("$islemTabloAdi.formId", $formId)
                ->get()
                ->toArray();

            $form = Formlar::find($formId);

            // Form işlemlerinin hepsi tamamlandığında formun bitisTarihi'ni ayarlar
            $durumlar = array_count_values(array_column($formIslemleri, "islemDurumKodu"));

            if (isset($durumlar["TAMAMLANDI"]) && $durumlar["TAMAMLANDI"] === count($formIslemleri))
            {
                $form->bitisTarihi = Carbon::now();
            }
            else
            {
                $form->bitisTarihi = null;
            }

            $form->save();

            foreach ($formIslemleri as $islem)
            {
                $this->siparisDurumKontrol($islem["siparisId"]);
            }

            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    public function pdfOlustur($dosyaAdi, $parametreler = [])
    {

        $altKlasor = "";

        $_payload = isset($parametreler["payload"]) && $parametreler["payload"] ? $parametreler["payload"] : [];

        if (isset($parametreler["klasor"]) && $parametreler["klasor"])
        {
            $altKlasor .= $parametreler["klasor"] . "/";
        }
        else
        {
            $altKlasor .= $parametreler["tur"] . "/";
        }

        $params = isset($parametreler["query"]) && $parametreler["query"] ? $parametreler["query"] : [];

        // $url = 'http://PhantomJScloud.com/api/browser/v2/a-demo-key-with-low-quota-per-ip-address/';
        $url = 'https://PhantomJScloud.com/api/browser/v2/ak-5ykcp-wxt7z-74k7b-8h7gv-c6548/';
        $payload = [
            "url" => url("/pdf-exports/" . $parametreler["tur"] . "?q=" . $params["q"], [], true),
            "renderType" => "pdf",
            "overseerScript" => '
                await page.waitForSelector(".printable-page");
                page.done()
            ',
            ...$_payload,
        ];
        // $output = new ConsoleOutput();
        // $output->writeln("<info>" . $payload["url"] . "</info>");
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($payload)
            ],
            'https' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($payload)
            ],
        ];


        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            return false;
        }

        Storage::disk('local')->put("public/" . $altKlasor . $dosyaAdi . ".pdf", $result);

        return $result;
    }

    public function pdfOlustur2($dosyaAdi, $altKlasor, $parametreler = [])
    {

        $_payload = isset($parametreler["payload"]) && $parametreler["payload"] ? $parametreler["payload"] : [];
        // dd(url("/pdf-exports2/" . $parametreler["tur"] ."/". $parametreler["id"], [], false));
        //$url = 'http://PhantomJScloud.com/api/browser/v2/a-demo-key-with-low-quota-per-ip-address/';
        $url = 'https://PhantomJScloud.com/api/browser/v2/ak-5ykcp-wxt7z-74k7b-8h7gv-c6548/';

        $payload = [
            "url" => url("/pdf-exports2/" . $parametreler["tur"] ."/". $parametreler["id"], [], false),
            "renderType" => "pdf",
            "overseerScript" => '
                await page.waitForSelector(".printable-page");
                page.done()
            ',
            ...$_payload,
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($payload)
            ],
            'https' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($payload)
            ],
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            return false;
        }

        $ek = 1;
        $klasor = public_path("pdf/") . $altKlasor;
        // $dosyaAdi =  . $dosyaAdi;

        $teklifUrl = $klasor . $dosyaAdi . $ek . ".pdf";
        while (File::exists($teklifUrl)) {
            $ek++;
            $teklifUrl = $klasor . $dosyaAdi . $ek . ".pdf";
        }

        // Hedef klasörü oluştur
        if (!File::isDirectory($klasor)) {
            File::makeDirectory($klasor, 0777, true);
        }

        $pdf = File::put($teklifUrl, $result);
        if ($pdf) {
            // return $teklifUrl;
            return "pdf/" . $altKlasor . $dosyaAdi . $ek .".pdf";
        }

        return false;
    }

    function XMLPOST($PostAddress, $xmlData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $PostAddress);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "data=" . $xmlData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $results = curl_exec($ch);
        return $results;
    }

    function smsSend($msg)
    {
        $sms_user = 'kullaniciAdi';
        $sms_pass = 'sifre';
        $sms_title = 'baslik';

        $xml = '
            <MultiTextSMS>
                <UserName>' . $sms_user . '</UserName>
                <PassWord>' . $sms_pass . '</PassWord>
                <Action>11</Action>
                <Messages>' . $msg . '</Messages>
                <Originator>' . $sms_title . '</Originator>
                <SDate></SDate>
            </MultiTextSMS>';
        //return $xml;
        $gelen = XMLPOST('http://www.smspaketim.com.tr/api/mesaj_gonder', $xml);
        return $gelen;
    }
}
