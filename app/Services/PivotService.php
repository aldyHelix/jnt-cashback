<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PivotService {
    /**
     * general summary
     */
    public function getSumAllBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.sum_all_biaya_kirim')) {
        //     return 0;
        // }
        return DB::table($schema.'.sum_all_biaya_kirim')
            ->first()->sum;
    }
    /**
     * CP DP
     */
    public function getPivotAllCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(Schema::hasTable($schema.'.cp_dp_all_count_sum')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.cp_dp_all_count_sum')
            ->get();
    }

    public function getPivotRegulerCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.cp_dp_reguler_count_sum')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.cp_dp_reguler_count_sum')
            ->get();
    }

    public function getPivotSuperCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.cp_dp_super_count_sum')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.cp_dp_super_count_sum')
            ->get();
    }
    public function getPivotDfodCountSumCPDP($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.cp_dp_dfod_count_sum')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.cp_dp_dfod_count_sum')
            ->get();
    }
    public function getPivotMPCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.cp_dp_mp_count_waybill')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.cp_dp_mp_count_waybill')
            ->get();
    }
    public function getPivotMPSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.cp_dp_mp_sum_biaya_kirim')) {
        //     return collect([]);

        // }
        return DB::table($schema.'.cp_dp_mp_sum_biaya_kirim')
            ->get();
    }
    public function getPivotMPReturCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.cp_dp_mp_retur_count_waybill')) {
        //     return collect([]);

        // }
        return DB::table($schema.'.cp_dp_mp_retur_count_waybill')
            ->get();
    }
    public function getPivotMPReturSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.cp_dp_mp_retur_sum_biaya_kirim')) {
        //     return collect([]);

        // }
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

        // if(!Schema::hasTable($schema.'.dpf_all_count_sum')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.dpf_all_count_sum')
            ->get();
    }

    public function getPivotDPFRegulerCountSum($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.dpf_reguler_count_sum')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.dpf_reguler_count_sum')
            ->get();
    }

    public function getPivotDPFSuperCountSum($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.dpf_super_count_sum')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.dpf_super_count_sum')
            ->get();
    }
    public function getPivotDPFDfodCountSum($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.dpf_dfod_count_sum')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.dpf_dfod_count_sum')
            ->get();
    }
    public function getPivotDPFMPCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.dpf_mp_count_waybill')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.dpf_mp_count_waybill')
            ->get();
    }
    public function getPivotDPFMPSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.dpf_mp_sum_biaya_kirim')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.dpf_mp_sum_biaya_kirim')
            ->get();
    }
    public function getPivotDPFMPReturCountWaybill($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.dpf_mp_retur_count_waybill')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.dpf_mp_retur_count_waybill')
            ->get();
    }
    public function getPivotDPFMPReturSumBiayaKirim($schema){
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.dpf_mp_retur_sum_biaya_kirim')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.dpf_mp_retur_sum_biaya_kirim')
            ->get();
    }

    public function getDeliverySprinter($schema) {
        if(!Schema::hasTable($schema.'.data_mart')) {
            return false;
        }

        // if(!Schema::hasTable($schema.'.mp_delivery_count_sprinter')) {
        //     return collect([]);
        // }
        return DB::table($schema.'.mp_delivery_count_sprinter')
            ->get();
    }
}
