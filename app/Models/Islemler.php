<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Islemler extends Model
{
    use SoftDeletes;

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
    ];
}
