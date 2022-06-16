<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class Roller extends Role
{
    use SoftDeletes;
}
