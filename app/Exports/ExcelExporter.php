<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExporter implements FromArray
{
    public $data = [];
    public $headers = [];

    protected $preparedData = [];
    protected $preparedHeaders = [];

    public function __construct($data, $headers = [], $manuel = false)
    {
        $this->data = $data;
        $this->headers = $headers;

        if (!$manuel)
        {
            $this->prepare();
        }

        return $this;
    }

    public function array(): array
    {
        return [
            $this->preparedHeaders,
            ...$this->preparedData,
        ];
    }

    public function downloadExcel($fileName)
    {
        return Excel::download($this, $fileName . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function prepare()
    {
        $this->prepareHeaders();

        $this->prepareValues();

        // dd([
        //     $this->preparedHeaders,
        //     ...$this->preparedData,
        // ]);
    }

    public function prepareHeaders($headers = null)
    {
        $headers = $headers ?? $this->headers;

        foreach ($headers as $value)
        {
            if (is_array($value))
            {
                $this->preparedHeaders[] = $value["value"];
            }
            else
            {
                $this->preparedHeaders[] = $value;
            }
        }

        return $headers;
    }

    public function prepareValues()
    {
        foreach ($this->data as $dataKey => $data)
        {
            foreach ($this->headers as $key => $value)
            {
                if (is_array($value))
                {
                    $v = $data[$value["key"]];

                    if (isset($value["tur"]))
                    {
                        if ($value["tur"] === "TARIH")
                        {
                            $v = Carbon::parse($v)->format("d.m.Y");
                        }
                    }
                }
                else
                {
                    $v = $data[$key];
                }

                $this->preparedData[$dataKey][] = $v;
            }
        }
    }
}