<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashbackSetting extends Model
{
    use HasFactory;

    public $table = 'cashback_setting';

    public $fillable = [
        'jenis_paket',
        'diskon'
    ];
}
