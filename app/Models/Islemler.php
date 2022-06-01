<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Islemler extends Model
{
    use SoftDeletes;

    protected $table = 'islemler';
}
