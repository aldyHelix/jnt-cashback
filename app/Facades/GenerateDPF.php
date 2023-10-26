<?php

namespace App\Facades;

use App\Services\GenerateDPFService;
use Illuminate\Support\Facades\Facade;

class GenerateDPF extends Facade {
    protected static function getFacadeAccessor()
    {
        return GenerateDPFService::class;
    }
}
