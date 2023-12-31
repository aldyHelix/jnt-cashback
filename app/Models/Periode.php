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
        'inserted_row',
        'processed_row',
        'count_row',
        'processed_by',
        'status',
        'is_processing_done',
        'is_pivot_processing_done',
        'is_locked',
        'processed_grade_1',
        'processed_grade_2',
        'processed_grade_3',
        'processed_grade_1_by',
        'processed_grade_2_by',
        'processed_grade_3_by',
        'locked_grade_1',
        'locked_grade_2',
        'locked_grade_3',
        'start_processed_at',
        'done_processed_at',
        'data_pivot',
        'data_pivot_mp',
        'data_pivot_vip',
        'data_cashback_reguler',
        'data_cashback_marketplace_cod',
        'data_cashback_marketplace_non_cod',
        'data_cashback_klien_vip',
        'data_cashback_grading_1',
        'data_cashback_grading_2',
        'data_cashback_grading_3',
    ];

    public function jsonData(){
        return $this->belongsTo('App\Models\PeriodeDataJson', 'id', 'periode_id');

    }
}

