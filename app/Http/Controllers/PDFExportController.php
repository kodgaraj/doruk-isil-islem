<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDFExportController extends Controller
{
    public function index($tur)
    {
        $view = "pdf-exports.$tur.index";

        if (!view()->exists($view))
        {
            return redirect()->route("home");
        }

        return view($view);
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
