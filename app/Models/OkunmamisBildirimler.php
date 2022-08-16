<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OkunmamisBildirimler extends Model
{
    protected $table = 'okunmamis_bildirimler';

    protected $casts = [
        'id' => 'integer',
        "bildirimId" => "integer",
        "kullaniciId" => "integer",
    ];

    public $timestamps = false;
}
