<?php

namespace App\Http\Controllers;

use App\Models\Sablonlar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Send;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class MailController extends Controller
{

    public function mail(Request $request)
    {

        $teklif = json_decode($request->teklif,true);
        $aliciMailAdres = $teklif["eposta"];
        $cc = $teklif["cc"] ?? "";
        $mesaj = $teklif["icerik_html"];
        $baslik = $teklif["teklifAdi"];
        $pathToFile = $teklif["url"];
        $dosyalar = $request->file('dosyalar') ? $request->file('dosyalar') : [];


        if(count($dosyalar) > 0){

            $klasor = public_path("dosyalar/");
            if (!File::isDirectory($klasor)) {
                File::makeDirectory($klasor, 0777, true);
            }
            foreach($dosyalar as $dosya){
                $dosyaAdi = str_replace(' ', '',$dosya->getClientOriginalName());
                $dosya->move(public_path("dosyalar/"), $dosyaAdi);
                $dosya->url = "dosyalar/" . $dosyaAdi;
            }
        }

        try {
            if (filter_var($aliciMailAdres, FILTER_VALIDATE_EMAIL)) {

                $gonder = Mail::to($aliciMailAdres)
                ->cc(isset($cc) && $cc != "" && filter_var($cc, FILTER_VALIDATE_EMAIL) ? $cc : null)
                ->send(new send($aliciMailAdres, $mesaj, $baslik, $pathToFile, $dosyalar));

                return response()->json([
                    'durum' => true,
                    'mesaj' => $aliciMailAdres . ' ve ' . $cc . ' adreslerine mail başarıyla gönderildi.',
                    'mail' => $gonder
                ]);

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
