<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siparisler extends Model
{
    use SoftDeletes;

    protected $table = 'siparisler';

    protected $casts = [
        "id" => "integer",
        "firmaId" => "integer",
        "durumId" => "integer",
        "userId" => "integer",
    ];
}
