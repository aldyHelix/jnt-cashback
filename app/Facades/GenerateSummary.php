<?php

namespace App\Facades;

use App\Services\GenerateSummaryService;
use Illuminate\Support\Facades\Facade;

class GenerateSummary extends Facade {
    protected static function getFacadeAccessor()
    {
        return GenerateSummaryService::class;
    }
}
