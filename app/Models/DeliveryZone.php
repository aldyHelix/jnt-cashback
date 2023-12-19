<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Collectionpoint\Models\Collectionpoint;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $table = 'delivery_zone';

    public $fillable = [
        'collection_point_id',
        'drop_point_outgoing',
        'drop_point_ttd',
        'kpi_target_count',
        'kpi_reduce_not_achievement',
        'is_show'
    ];

    public function collection_point(){
        return $this->belongsTo(Collectionpoint::class, 'collection_point_id', 'id');
    }
}
