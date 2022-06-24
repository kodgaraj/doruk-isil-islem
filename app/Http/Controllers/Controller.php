<?php

namespace App\Http\Controllers;

use App\Models\Formlar;
use App\Models\IslemDurumlari;
use App\Models\Islemler;
use App\Models\Siparisler;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

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
}
