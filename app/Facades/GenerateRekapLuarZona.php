<?php

namespace App\Facades;

use App\Services\GeneratePivotZonasiService;
use Illuminate\Support\Facades\Facade;

class GenerateRekapLuarZona extends Facade {
    protected static function getFacadeAccessor()
    {
        return GeneratePivotZonasiService::class;
    }
}
