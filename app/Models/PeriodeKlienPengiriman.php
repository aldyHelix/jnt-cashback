<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeKlienPengiriman extends Model
{
    use HasFactory;

    protected $table = 'periode_klien_pengiriman';

    protected $fillable = [
        'periode_id',
        'klien_pengiriman_id',
        'category_id',
    ];

    public function category(){
        return $this->belongsToMany('Modules\Category\Model\CategoryKlienPengiriman');
    }

    public function klien_pengiriman(){
        return $this->belongsTo('App\Models\GlobalKlienPengiriman');
    }

    public function each_category(){
        return $this->hasOne('App\Models\CategoryKlienPengiriman');
    }
}
