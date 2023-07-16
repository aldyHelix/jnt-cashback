<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Pivot {
    /**
     * CP DP
     */
    public function getPivotAllCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.cp_dp_all_count_sum')
            ->get();
    }

    public function getPivotRegulerCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.cp_dp_reguler_count_sum')
            ->get();
    }

    public function getPivotSuperCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.cp_dp_super_count_sum')
            ->get();
    }
    public function getPivotDfodCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.cp_dp_dfod_count_sum')
            ->get();
    }
    public function getPivotMPCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.cp_dp_mp_count_waybill')
            ->get();
    }
    public function getPivotMPSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.cp_dp_mp_sum_biaya_kirim')
            ->get();
    }
    public function getPivotMPReturCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.cp_dp_mp_retur_count_waybill')
            ->get();
    }
    public function getPivotMPReturSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.cp_dp_mp_retur_sum_biaya_kirim')
            ->get();
    }
    /**
     * DPF
     */
    public function getPivotDPFAllCountSum($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.dpf_all_count_sum')
            ->get();
    }

    public function getPivotDPFRegulerCountSum($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.dpf_reguler_count_sum')
            ->get();
    }

    public function getPivotDPFSuperCountSum($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.dpf_super_count_sum')
            ->get();
    }
    public function getPivotDPFDfodCountSum($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.dpf_dfod_count_sum')
            ->get();
    }
    public function getPivotDPFMPCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.dpf_mp_count_waybill')
            ->get();
    }
    public function getPivotDPFMPSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.dpf_mp_sum_biaya_kirim')
            ->get();
    }
    public function getPivotDPFMPReturCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.dpf_mp_retur_count_waybill')
            ->get();
    }
    public function getPivotDPFMPReturSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }
        return DB::table($schema.'.dpf_mp_retur_sum_biaya_kirim')
            ->get();
    }
}
