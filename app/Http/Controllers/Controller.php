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
        $islemTabloAdi = (new Islemler())->getTable();
        $formTabloAdi = (new Formlar())->getTable();
        $islemDurumTabloAdi = (new IslemDurumlari())->getTable();
        $siparisTabloAdi = (new Siparisler())->getTable();

        $form = Formlar::join($islemTabloAdi, $formTabloAdi . '.id', '=', $islemTabloAdi . '.formId')
            ->where("$islemTabloAdi.id", $islemId)
            ->first();

        if (!$form)
        {
            return false;
        }

        $tamamlanmamisFormIslemler = Islemler::join($islemDurumTabloAdi, $islemDurumTabloAdi . '.id', '=', $islemTabloAdi . '.durumId')
            ->where("$islemTabloAdi.formId", $form->id)
            ->where("$islemDurumTabloAdi.kod", "<>", "TAMAMLANDI")
            ->count();

        if ($tamamlanmamisFormIslemler === 0)
        {
            $form->bitisTarihi = Carbon::now();

            if (!$form->save())
            {
                return false;
            }
        }

        return true;
    }
}
