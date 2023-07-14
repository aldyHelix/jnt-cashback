<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DendaDelivery extends Model
{
    use HasFactory;

    public $table = 'denda_delivery_periode';

    protected $fillable = [
        'periode_id',
        'sprinter_pickup',
        'transit_fee',
        'denda_void',
        'denda_dfod',
        'denda_pusat',
        'denda_selisih_berat',
        'denda_lost_scan_kirim',
        'denda_auto_claim',
        'denda_sponsorship',
        'denda_late_pickup_ecommerce',
        'potongan_pop',
        'denda_lainnya',
    ];

    public function periode(){
        $this->belongsTo(Cashback::class, 'periode_id', 'id');
    }
}
