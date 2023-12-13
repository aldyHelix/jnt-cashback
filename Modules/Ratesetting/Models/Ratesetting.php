<?php

namespace Modules\Ratesetting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratesetting extends Model
{
    use HasFactory;

    public $table = 'master_setting_tarif';

    public $fillable = [
        'grading_type',
        'sumber_waybill',
        'diskon_persen',
        'fee'
    ];

    /**
     * Ratesetting Factory
     *
     * @return \Modules\Ratesetting\Databases\Factories\RatesettingFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\Ratesetting\Databases\Factories\RatesettingFactory::new();
    }
}
