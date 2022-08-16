<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class IslemTurleri extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'islem_turleri';

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
                $aciklama = "İşlem türü oluşturuldu.";
                break;
            case "updated":
                $aciklama = "İşlem türü güncellendi.";
                break;
            case "deleted":
                $aciklama = "İşlem türü silindi.";
                break;
        }
        return $aciklama;
    }
}
