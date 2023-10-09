<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalKatResi extends Model
{
    use HasFactory;

    protected $table = 'global_category_resi';

    public $fillable = [
        'kat'
    ];
}
