<?php

namespace Modules\Period\Http\Controllers;

use App\Facades\PivotTable;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Period\Datatables\PeriodDatatables;

class PeriodeController extends Controller
{
    public function index(){
        ladmin()->allows(['ladmin.periode.index']);

        if( request()->has('datatables') ) {
            return PeriodDatatables::renderData();
        }

        return view('period::index');
    }

    public function viewDetail($code) {
        $data['periode'] = Periode::where('code', $code)->first();
        $data['row_total'] = DB::table($data['periode']->code.'.data_mart')->count();
        $data['cp_grade_a'] = DB::table('master_collection_point AS cp')->join($data['periode']->code.'.all_count_sum_cp_dp AS pivot', 'cp.drop_point_outgoing', '=', 'pivot.drop_point_outgoing')->select('cp.kode_cp', 'cp.nama_cp', 'pivot.count', 'pivot.sum')->where('cp.grading_pickup', 'A')->get();
        $data['cp_grade_b'] = DB::table('master_collection_point AS cp')->join($data['periode']->code.'.all_count_sum_cp_dp AS pivot', 'cp.drop_point_outgoing', '=', 'pivot.drop_point_outgoing')->select('cp.kode_cp', 'cp.nama_cp', 'pivot.count', 'pivot.sum')->where('cp.grading_pickup', 'B')->get();
        $data['cp_grade_c'] = DB::table('master_collection_point AS cp')->join($data['periode']->code.'.all_count_sum_cp_dp AS pivot', 'cp.drop_point_outgoing', '=', 'pivot.drop_point_outgoing')->select('cp.kode_cp', 'cp.nama_cp', 'pivot.count', 'pivot.sum')->where('cp.grading_pickup', 'C')->get();
        $data['mp_count_waybill'] = PivotTable::getPivotMPCountWaybill($code);
        $data['mp_sum_biaya_kirim'] = PivotTable::getPivotMPSumBiayaKirim($code);
        $data['mp_retur_count_waybill'] = PivotTable::getPivotMPReturCountWaybill($code);
        $data['mp_retur_sum_biaya_kirim'] = PivotTable::getPivotMPReturSumBiayaKirim($code);
        $data['all_summary'] = PivotTable::getPivotAllCountSumCPDP($code);
        $data['reguler_summary'] = PivotTable::getPivotRegulerCountSumCPDP($code);
        $data['dfod_summary'] = PivotTable::getPivotDfodCountSumCPDP($code);
        $data['super_summary'] = PivotTable::getPivotSuperCountSumCPDP($code);
        $data['total'] = [
            'all_summary_total_count' => $data['all_summary']->sum('count'),
            'all_summary_total_sum' => $data['all_summary']->sum('sum'),
            'reguler_summary_total_count' => $data['reguler_summary']->sum('count'),
            'reguler_summary_total_sum' => $data['reguler_summary']->sum('sum'),
            'super_summary_total_count' => $data['super_summary']->sum('count'),
            'super_summary_total_sum' => $data['super_summary']->sum('sum'),
            'dfod_summary_total_count' => $data['dfod_summary']->sum('count'),
            'dfod_summary_total_sum' => $data['dfod_summary']->sum('sum'),
            'grade_a_summary_total_count' => $data['cp_grade_a']->sum('count'),
            'grade_a_summary_total_sum' => $data['cp_grade_a']->sum('sum'),
            'grade_b_summary_total_count' => $data['cp_grade_b']->sum('count'),
            'grade_b_summary_total_sum' => $data['cp_grade_b']->sum('sum'),
            'grade_c_summary_total_count' => $data['cp_grade_c']->sum('count'),
            'grade_c_summary_total_sum' => $data['cp_grade_c']->sum('sum'),
        ];
        return view('period::summary-periode', $data);
    }
}
