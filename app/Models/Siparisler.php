<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Siparisler extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'siparisler';

    protected $casts = [
        "id" => "integer",
        "firmaId" => "integer",
        "durumId" => "integer",
        "userId" => "integer",
        "terminSuresi" => "integer",
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
                $aciklama = "Sipariş oluşturuldu.";
                break;
            case "updated":
                $aciklama = "Sipariş güncellendi.";
                break;
            case "deleted":
                $aciklama = "Sipariş silindi.";
                break;
        }
        return $aciklama;
    }
}
