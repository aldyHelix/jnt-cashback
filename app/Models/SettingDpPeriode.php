<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingDpPeriode extends Model
{
    use HasFactory;

    protected $table = "setting_dp_periode";

    public $fillable = [
        'periode_id',
        'drop_point_outgoing',
        'pengurangan_total',
        'penambahan_total',
        'diskon_cod',
        'grouping',
        'grading_type',
        'retur_klien_pengirim_hq',
        'retur_belum_terpotong'
    ];
}
