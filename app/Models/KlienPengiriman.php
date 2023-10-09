<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlienPengiriman extends Model
{
    use HasFactory;

    protected $table = 'master_klien_pengiriman_setting';

    protected $fillable = [
        'periode_id',
        'klien_pengiriman',
        'is_reguler',
        'is_dfod',
        'is_super'
    ];

    public function category(){
        return $this->belongsToMany('Modules\Category\Model\CategoryKlienPengiriman');
    }
}
