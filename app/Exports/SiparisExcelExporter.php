<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;

class SiparisExcelExporter implements FromView
{
    public $siparisler = [];
    public $siparisBasliklari = [];
    public $islemBasliklari = [];
    public $preparedSiparisBasliklari = [];
    public $preparedIslemBasliklari = [];

    public function __construct($data, $headers = [])
    {
        $this->siparisler = $data;
        $this->siparisBasliklari = $headers["siparis"];
        $this->islemBasliklari = $headers["islem"];
        $this->prepareHeaders($this->siparisBasliklari, "preparedSiparisBasliklari");
        $this->prepareHeaders($this->islemBasliklari, "preparedIslemBasliklari");

        return $this;
    }

    public function view(): View
    {
        // dd($this->siparisler);
        return view("excel-exports.siparis", [
            "siparisler" => $this->siparisler,
            "siparisKeyleri" => array_keys($this->siparisBasliklari),
            "siparisBasliklari" => $this->preparedSiparisBasliklari,
            "islemKeyleri" => array_keys($this->islemBasliklari),
            "islemBasliklari" => $this->preparedIslemBasliklari,
        ]);
    }

    public function downloadExcel($dosyaAdi = "Sipariş Listesi")
    {
        return Excel::download($this, $dosyaAdi . '.xlsx');
    }

    public function prepareHeaders($headers, $var)
    {
        foreach ($headers as $value)
        {
            if (is_array($value))
            {
                $this->{$var}[] = $value["value"];
            }
            else
            {
                $this->{$var}[] = $value;
            }
        }
    }
}