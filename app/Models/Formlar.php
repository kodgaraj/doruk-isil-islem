<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Formlar extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'formlar';

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
                $aciklama = "Isıl işlem formu oluşturuldu.";
                break;
            case "updated":
                $aciklama = "Isıl işlem formu güncellendi.";
                break;
            case "deleted":
                $aciklama = "Isıl işlem formu silindi.";
                break;
        }
        return $aciklama;
    }
}
