<?php

namespace Modules\RateSetting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryFee extends Model
{
    use HasFactory;

    public $table = 'master_delivery_fee';

    public $fillable = [
        'zona',
        'tarif',
    ];

    /**
     * DeliveryFee Factory
     *
     * @return \Modules\RateSetting\Databases\Factories\DeliveryFeeFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\RateSetting\Databases\Factories\DeliveryFeeFactory::new();
    }
}
