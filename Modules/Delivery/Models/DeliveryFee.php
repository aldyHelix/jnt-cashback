<?php

namespace Modules\Delivery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryFee extends Model
{
    use HasFactory;

    /**
     * DeliveryFee Factory
     *
     * @return \Modules\Delivery\Databases\Factories\DeliveryFeeFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\Delivery\Databases\Factories\DeliveryFeeFactory::new();
    }
}
