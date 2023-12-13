<?php

namespace Modules\Ratesetting\Models;

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
     * @return \Modules\Ratesetting\Databases\Factories\DeliveryFeeFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\Ratesetting\Databases\Factories\DeliveryFeeFactory::new();
    }
}
