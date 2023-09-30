<?php

namespace App\Facades;

use App\Services\GenerateSchemaService;
use Illuminate\Support\Facades\Facade;

class GenerateSchema extends Facade {
    protected static function getFacadeAccessor()
    {
        return GenerateSchemaService::class;
    }
}
