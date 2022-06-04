<?php

namespace App\Http\Controllers;

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
}
