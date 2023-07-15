<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Pivot {
    public function getPivotAllCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.all_count_sum_cp_dp')
            ->get();
    }

    public function getPivotRegulerCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.reguler_count_sum_cp_dp')
            ->get();
    }

    public function getPivotSuperCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.super_count_sum_cp_dp')
            ->get();
    }
    public function getPivotDfodCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.dfod_count_sum_cp_dp')
            ->get();
    }
    public function getPivotMPCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.mp_count_waybill_cp_dp')
            ->get();
    }
    public function getPivotMPSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.mp_sum_biaya_kirim')
            ->get();
    }
    public function getPivotMPReturCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.mp_count_no_waybill')
            ->get();
    }
    public function getPivotMPReturSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.mp_retur_sum_biaya_kirim')
            ->get();
    }
}
