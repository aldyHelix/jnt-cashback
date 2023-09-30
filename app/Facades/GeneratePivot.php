<?php

namespace App\Facades;

use App\Services\GeneratePivotTableService;
use Illuminate\Support\Facades\Facade;

class GeneratePivot extends Facade {
    protected static function getFacadeAccessor()
    {
        return GeneratePivotTableService::class;
    }
}
