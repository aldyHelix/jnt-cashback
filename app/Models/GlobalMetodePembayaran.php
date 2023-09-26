<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalMetodePembayaran extends Model
{
    use HasFactory;

    protected $table = 'global_metode_pembayaran';

    public $fillable = [
        'metode_pembayaran',
    ];
}
