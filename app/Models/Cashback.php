<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashback extends Model
{
    use HasFactory;

    public $fillable = [
        'no_waybill',
        'tanggal_pengiriman',
        'drop_point_outgoing',
        'sprinter_pickup',
        'tujuan',
        'berat_ditagih','biaya_cod',
        'biaya_asuransi',
        'biaya_kirim',
        'biaya_lainnya',
        'total_biaya' ,
        'klien_pengirim' ,
        'cara_pembayaran' ,
        'nama_pengirim' ,
        'alamat_pengirim' ,
        'sumber_waybill' ,
        'retur' ,
        'waktu_ttd' ,
        'nominal_diskon' ,
        'biaya_setelah_diskon' ,
        'zona',
    ];
}
