<?php

namespace Modules\UploadFile\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    use HasFactory;

    public $table = 'file_upload';

    public $fillable = [
        'file_name',
        'month_period',
        'year_period',
        'processed_row',
        'count_row',
        'file_size',
        'table_name',
        'processed_by',
        'processing_status',
        'type_file',
        'is_processing_done',
        'is_pivot_processing_done',
        'is_locked',
        'start_processed_at',
        'done_processed_at'
    ];
    /**
     * UploadFile Factory
     *
     * @return \Modules\UploadFile\Databases\Factories\UploadFileFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\UploadFile\Databases\Factories\UploadFileFactory::new();
    }
}
