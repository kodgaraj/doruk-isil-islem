<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Islemler extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'islemler';

    protected $casts = [
        'id' => 'integer',
        "siparisId" => "integer",
        "malzemeId" => "integer",
        "durumId" => "integer",
        "formId" => "integer",
        "firinId" => "integer",
        "islemTuruId" => "integer",
        "tekrarEdenId" => "integer",
        "tekrarEdilenId" => "integer",
        "siraNo" => "integer",
        "adet" => "integer",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(["*"])
        ->setDescriptionForEvent(fn (string $eventName) => $this->aciklamaOlustur($eventName));
        // Chain fluent methods for configuration options
    }

    public function aciklamaOlustur($eventName)
    {
        $aciklama = "";
        switch ($eventName) {
            case "created":
                $aciklama = "İşlem oluşturuldu.";
                break;
            case "updated":
                $aciklama = "İşlem güncellendi.";
                break;
            case "deleted":
                $aciklama = "İşlem silindi.";
                break;
        }
        return $aciklama;
    }
}
