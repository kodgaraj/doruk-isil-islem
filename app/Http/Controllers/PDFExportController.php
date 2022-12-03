<?php

namespace App\Http\Controllers;

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

    public function createPDF()
    {
        $this->pdfOlustur("deneme", ["tur" => "teklifler"]);

        return "PDF Oluşturuldu!";
    }
}
