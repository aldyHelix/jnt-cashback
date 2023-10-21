<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeDataJson extends Model
{
    use HasFactory;

    protected $table = 'periode_data_json';

    protected $fillable = [
        'periode_id',
        'pivot_',
        'pivot_mp',
        'pivot_vip',
        'cashback_reguler_a',
        'cashback_reguler_b',
        'cashback_reguler_c',
        'cashback_marketplace_cod',
        'cashback_marketplace_awb_cod',
        'cashback_marketplace_awb_g3_cod',
        'cashback_marketplace_non_cod',
        'cashback_klien_vip',
        'cashback_luar_zona',
        'cashback_setting',
        'cashback_grading_1',
        'cashback_grading_1_denda',
        'cashback_grading_2',
        'cashback_grading_2_denda',
        'cashback_grading_3',
        'cashback_grading_3_denda',
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
