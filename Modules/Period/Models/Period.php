<?php

namespace Modules\Period\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    /**
     * Period Factory
     *
     * @return \Modules\Period\Databases\Factories\PeriodFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\Period\Databases\Factories\PeriodFactory::new();
    }
}
