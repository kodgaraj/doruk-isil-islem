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
        $cc = $request->teklif["cc"] ?? "";
        $mesaj = $request->teklif["icerik_html"];
        $baslik = $request->teklif["teklifAdi"];
        $pathToFile = $request->teklif["url"];
        try {
            if (filter_var($aliciMailAdres, FILTER_VALIDATE_EMAIL)) {
                if(isset($cc) && $cc != "" && filter_var($cc, FILTER_VALIDATE_EMAIL)){
                    $gonder = Mail::to($aliciMailAdres)
                    ->cc($cc)
                    ->send(new send($aliciMailAdres, $mesaj, $baslik, $pathToFile));
                    return response()->json([
                        'durum' => true,
                        'mesaj' => $aliciMailAdres . ' ve ' . $cc . ' adreslerine mail başarıyla gönderildi.',
                        'mail' => $gonder
                    ]);
                }else{
                    $gonder = Mail::to($aliciMailAdres)
                    ->send(new send($aliciMailAdres, $mesaj, $baslik, $pathToFile));
                    return response()->json([
                        'durum' => true,
                        'mesaj' => $aliciMailAdres . ' adresine mail başarıyla gönderildi.',
                        'mail' => $gonder
                    ]);}
            } else {
                return response()->json([
                    'durum' => false,
                    'mesaj' => "Geçerli bir e-posta adresi değil."
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }


    }
}
