<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\RefreshesPermissionCache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Roller extends Role
{
    use SoftDeletes, RefreshesPermissionCache, LogsActivity;

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
                $aciklama = "Rol oluşturuldu.";
                break;
            case "updated":
                $aciklama = "Rol güncellendi.";
                break;
            case "deleted":
                $aciklama = "Rol silindi.";
                break;
        }
        return $aciklama;
    }
}
