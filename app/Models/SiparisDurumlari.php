<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiparisDurumlari extends Model
{
    protected $table = 'siparis_durumlari';

    protected $casts = [
        'id' => 'integer',
    ];
}
