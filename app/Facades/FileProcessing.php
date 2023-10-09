<?php

namespace App\Facades;

use App\Services\FileProcessingService;
use Illuminate\Support\Facades\Facade;

class FileProcessing extends Facade {
    protected static function getFacadeAccessor()
    {
        return FileProcessingService::class;
    }
}
