<?php

namespace Modules\Category\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryKlienPengiriman extends Model
{
    use HasFactory;

    protected $table = 'master_category';
    protected $fillable = ['nama_kategori', 'kode_kategori'];
    /**
     * CategoryKlienPengiriman Factory
     *
     * @return \Modules\Category\Databases\Factories\CategoryKlienPengirimanFactory;
     */
    protected static function newFactory()
    {
        // return \Modules\Category\Databases\Factories\CategoryKlienPengirimanFactory::new();
    }

    public function klien_pengiriman(){
        return $this->belongsToMany('App\Models\GlobalKlienPengiriman', 'category_klien_pengiriman', 'klien_pengiriman_id', 'category_id', 'id', 'id');
    }
}
