<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogResi extends Model
{
    use HasFactory;
    public $table = 'log_resi';

    protected $fillable = [
        'periode_id',
        'batch_id',
        'resi',
        'before_raw',
        'after_raw',
        'type',
        'date'
    ];
}
