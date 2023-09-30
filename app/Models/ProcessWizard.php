<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessWizard extends Model
{
    use HasFactory;

    public $table = "periode_process_state";

    protected $fillable = [
        'periode_id',
        'process_percentage',
        'file_count',
        'global_setting_done',
        'upload_done',
        'setting_periode_done',
        'upload_process_done',
        'uninserted_resi_count',
        'resi_error_count',
        'is_pivot_done',
        'is_grading_done',
        'is_summary_done',
        'is_report_done',
        'is_locked'
    ];
}
