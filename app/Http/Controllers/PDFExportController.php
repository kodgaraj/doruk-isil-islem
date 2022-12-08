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
            $pdf = $this->pdfOlustur("deneme", [
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
                ], 200);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'PDF başarılı bir şekilde oluşturuldu.',
                "data" => $pdf,
            ], 200);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage(),
                "satir" => $e->getLine(),
                "hataKodu" => "CPDF001",
            ], 500);
        }
    }
}
