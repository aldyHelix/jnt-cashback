<?php

namespace App\Facades;

use App\Services\GeneratePivotRekapService;
use Illuminate\Support\Facades\Facade;

class GeneratePivotRekap extends Facade {
    protected static function getFacadeAccessor()
    {
        return GeneratePivotRekapService::class;
    }
}
