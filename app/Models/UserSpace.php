<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

final class UserSpace extends Pivot
{
    public $incrementing = true;
}
