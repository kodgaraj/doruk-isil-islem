<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bildirimler extends Model
{
    protected $table = 'bildirimler';

    protected $casts = [
        'id' => 'integer',
    ];
}
