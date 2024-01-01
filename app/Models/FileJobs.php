<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileJobs extends Model
{
    use HasFactory;

    public $fillable = [
        'path',
        'schema_name',
        'file_name',
        'disk',
        'file_hash',
        'extension',
        'collection',
        'type_file',
        'is_uploaded',
        'is_imported',
        'is_schema_created',
        'size',
    ];
}
