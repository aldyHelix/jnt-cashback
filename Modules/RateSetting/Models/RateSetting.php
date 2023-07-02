<?php

namespace Modules\RateSetting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateSetting extends Model
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
     * RateSetting Factory
     *
     * @return \Modules\RateSetting\Databases\Factories\RateSettingFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\RateSetting\Databases\Factories\RateSettingFactory::new();
    }
}
