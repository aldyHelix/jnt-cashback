<?php

namespace App\Facades;

use App\Services\CreateSchemaService;
use Illuminate\Support\Facades\Facade;

class CreateSchema extends Facade {
    protected static function getFacadeAccessor()
    {
        return CreateSchemaService::class;
    }
}
