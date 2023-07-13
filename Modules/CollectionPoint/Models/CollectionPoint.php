<?php

namespace Modules\CollectionPoint\Models;

use App\Models\Denda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionPoint extends Model
{
    use HasFactory;

    protected $table = 'master_collection_point';

    public $fillable = [
        'kode_cp',
        'nama_cp',
        'nama_pt',
        'drop_point_outgoing',
        'grading_pickup',
        'zona_delivery',
        'nomor_rekening',
        'nama_bank',
        'nama_rekening',
    ];

    /**
     * CollectionPoint Factory
     *
     * @return \Modules\CollectionPoint\Databases\Factories\CollectionPointFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\CollectionPoint\Databases\Factories\CollectionPointFactory::new();
    }

    public function denda()
    {
        return $this->hasMany(Denda::class, 'sprinter_pickup', 'id')
        ->where('grading_type', 1)
        ->where('period_id', $id);
    }
}
