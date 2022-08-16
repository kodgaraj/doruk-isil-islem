<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Firmalar extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'firmalar';

    protected $casts = [
        "id" => "integer",
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
                $aciklama = "Firma oluşturuldu.";
                break;
            case "updated":
                $aciklama = "Firma güncellendi.";
                break;
            case "deleted":
                $aciklama = "Firma silindi.";
                break;
        }
        return $aciklama;
    }
}
