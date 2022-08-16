<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Malzemeler extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'malzemeler';

    protected $casts = [
        'id' => 'integer',
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
                $aciklama = "Malzeme oluşturuldu.";
                break;
            case "updated":
                $aciklama = "Malzeme güncellendi.";
                break;
            case "deleted":
                $aciklama = "Malzeme silindi.";
                break;
        }
        return $aciklama;
    }
}
