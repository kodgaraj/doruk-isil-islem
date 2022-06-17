<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\RefreshesPermissionCache;

class Roller extends Role
{
    use SoftDeletes, RefreshesPermissionCache;
}
