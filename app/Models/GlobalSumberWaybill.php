<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSumberWaybill extends Model
{
    use HasFactory;

    protected $table = 'global_sumber_waybill';

    public $fillable = [
        'sumber_waybill',
    ];
}
