<?php

namespace Modules\Collectionpoint\Models;

use App\Models\Denda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collectionpoint extends Model
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
     * Collectionpoint Factory
     *
     * @return \Modules\Collectionpoint\Databases\Factories\CollectionpointFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\Collectionpoint\Databases\Factories\CollectionpointFactory::new();
    }

    public function denda()
    {
        return $this->hasMany(Denda::class, 'sprinter_pickup', 'id')
        ->where('grading_type', 1)
        ->where('period_id', $id);
    }
}
