<?php

namespace App\Facades;

use App\Services\Pivot;
use Illuminate\Support\Facades\Facade;

class PivotTable extends Facade {
    protected static function getFacadeAccessor()
    {
        return Pivot::class;
    }
}
