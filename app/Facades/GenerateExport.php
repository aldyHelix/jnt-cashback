<?php

namespace App\Facades;

use App\Services\GenerateExportService;
use Illuminate\Support\Facades\Facade;

class GenerateExport extends Facade {
    protected static function getFacadeAccessor()
    {
        return GenerateExportService::class;
    }
}
