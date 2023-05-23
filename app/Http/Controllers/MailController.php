<?php

namespace App\Http\Controllers;

use App\Models\Sablonlar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Send;

class MailController extends Controller
{

    public function mail(Request $request)
    {
        $aliciMailAdres = $request->teklif["eposta"];
        $mesaj = $request->teklif["icerik_html"];
        $baslik = $request->teklif["teklifAdi"];
        $pathToFile = $request->teklif["url"];
        try {
            $gonder = Mail::to($aliciMailAdres)
            ->send(new send($aliciMailAdres, $mesaj, $baslik, $pathToFile));

            return response()->json([
                'durum' => true,
                'mesaj' => 'Mail başarıyla gönderildi.',
                'mail' => $gonder
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }
}
