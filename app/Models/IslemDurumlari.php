<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IslemDurumlari extends Model
{
    use SoftDeletes;

    protected $table = 'islem_durumlari';
}
