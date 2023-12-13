<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalDroppointoutgoing extends Model
{
    use HasFactory;

    protected $table = 'global_drop_point_outgoing';

    protected $fillable = [
        'drop_point_outgoing',
    ];
}
