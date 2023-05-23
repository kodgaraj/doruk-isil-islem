<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Teklifler extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teklifler';
}
