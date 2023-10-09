<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalKlienPengiriman extends Model
{
    use HasFactory;

    protected $table = 'global_klien_pengiriman';

    protected $fillable = [
        'klien_pengiriman',
    ];

    public function category(){
        return $this->belongsToMany('Modules\Category\Models\CategoryKlienPengiriman', 'category_klien_pengiriman', 'klien_pengiriman_id', 'category_id', 'id', 'id');
    }
}
