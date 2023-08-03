<?php

namespace App\Facades;

use App\Services\PivotService;
use Illuminate\Support\Facades\Facade;

class PivotTable extends Facade {
    protected static function getFacadeAccessor()
    {
        return PivotService::class;
    }
}
