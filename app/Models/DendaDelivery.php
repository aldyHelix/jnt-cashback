<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DendaDelivery extends Model
{
    use HasFactory;

    public $table = 'denda_delivery_periode';

    protected $fillable = [
        'delivery_periode_id',
        'collection_point_id',
        'drop_point_outgoing',
        'denda_lost_scan_kirim',
        'denda_auto_claim',
        'denda_late_pickup_reg',
        'tarif',
        'dpp',
        'admin_bank',
    ];

    public function periode(){
        $this->belongsTo(Cashback::class, 'periode_id', 'id');
    }
}
