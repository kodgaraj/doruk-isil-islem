<?php

namespace App\Http\Controllers;

use App\Models\Teklifler;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Dompdf\Dompdf;
class PDFExportController extends Controller
{
    public function index($tur)
    {
        try
        {
            $view = "pdf-exports.$tur.index";
            if (!view()->exists($view))
            {
                return redirect()->route("home");
            }

            return view($view);
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }

    }
    public function index2($tur,$id)
    {
        try
        {
            $view = "pdf-exports.$tur.sablon";
            $teklif = Teklifler::find($id);
            $teklif->icerik_html = json_decode($teklif->icerik_html,true);
            if (!view()->exists($view))
            {
                return redirect()->route("home");
            }

            return view($view)->with("teklif",$teklif);
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }

    }
    public function createPDF(Request $request)
    {
        try
        {
            $dosyaAdi = $request->dosyaAdi;

            $pdf = $this->pdfOlustur($dosyaAdi, [
                "tur" => "teklifler",
                "query" => [
                    "q" => $request->data,
                ],
                "payload" => [
                    "renderSettings" => [
                        "emulateMedia" => 'print',
                        "pdfOptions" => [
                            "format" => 'letter',
                            "preferCSSPageSize" => true,
                            "margin" => [
                                "top" => 0,
                                "bottom" => 0,
                                "left" => 0,
                                "right" => 0
                            ],
                            "zoomFactor" => 1,
                        ],
                    ],
                ],
            ]);

            if ($pdf === false)
            {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'PDF oluşturulurken bir hata oluştu!',
                    "hataKodu" => "CPDF002",
                ], 400);
            }

            return response()->file(storage_path("/app/public/teklifler/" . $dosyaAdi . ".pdf"));
        }
        catch (\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => "İşlem sırasında bir hata oluştu",
                "hataKodu" => "CPDF001",
            ], 500);
        }

    }
}
