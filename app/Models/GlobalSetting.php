<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    use HasFactory;

    public $table = "general_setting";

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'parameter',
        'value',
        'order',
        'unit_name',
        'unit_char',
        'description',
    ];
}
