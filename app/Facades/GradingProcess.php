<?php

namespace App\Facades;

use App\Services\GradingService;
use Illuminate\Support\Facades\Facade;

class GradingProcess extends Facade {
    protected static function getFacadeAccessor()
    {
        return GradingService::class;
    }
}
