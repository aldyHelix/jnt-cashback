<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    public $table = "master_periode";

    protected $fillable = [
        'code',
        'month',
        'year',
        'processed_row',
        'count_row',
        'processed_by',
        'status',
        'is_processing_done',
        'is_pivot_processing_done',
        'is_locked',
        'start_processed_at',
        'done_processed_at',
    ];
}

