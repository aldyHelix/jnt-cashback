<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectFee extends Model
{
    use HasFactory;

    protected $table = 'delivery_may_2023.direct_fee';

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
}
