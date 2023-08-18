<?php

namespace Modules\Period\Http\Controllers;

use App\Facades\CreateSchema;
use App\Facades\PivotTable;
use App\Models\KlienPengiriman;
use App\Models\LogResi;
use App\Models\Periode;
use App\Models\SettingDpPeriode;
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

    public function viewSetting($code) {
        $data['periode'] = Periode::where('code', $code)->first();

        $data['dp_data_mart'] = DB::table($code.'.data_mart')->selectRaw('DISTINCT(drop_point_outgoing)')
        ->orderBy('drop_point_outgoing')->get();

        $data['dp'] = DB::table($code.'.data_mart')
            ->selectRaw('DISTINCT(data_mart.drop_point_outgoing), COALESCE(setting_dp_periode.id, 0) as id , COALESCE(setting_dp_periode.pengurangan_total, 0) as pengurangan_total, COALESCE(setting_dp_periode.penambahan_total, 0) as penambahan_total, COALESCE(setting_dp_periode.diskon_cod, 0) as diskon_cod')
            ->leftJoin('setting_dp_periode', function ($join) use ($data) {
                $join->on('setting_dp_periode.drop_point_outgoing', '=', 'data_mart.drop_point_outgoing')
                    ->where('setting_dp_periode.periode_id', $data['periode']->id);
            })
            ->orderBy('data_mart.drop_point_outgoing')
            ->get();


        $data['sumber_waybill'] = DB::table($code.'.data_mart')->selectRaw('DISTINCT(sumber_waybill)')->orderBy('sumber_waybill')->pluck('sumber_waybill')->toArray();

        $data['klien_pengiriman'] = DB::table($code.'.data_mart'
        )->selectRaw('DISTINCT(data_mart.klien_pengiriman), COALESCE(master_klien_pengiriman_setting.is_reguler, 0) as is_reguler, COALESCE(master_klien_pengiriman_setting.is_dfod, 0) as is_dfod, COALESCE(master_klien_pengiriman_setting.is_super, 0) as is_super')
        ->leftJoin('master_klien_pengiriman_setting', function ($join) use ($data) {
            $join->on('master_klien_pengiriman_setting.klien_pengiriman', '=', 'data_mart.klien_pengiriman')
                ->where('master_klien_pengiriman_setting.periode_id', $data['periode']->id);
        })
        ->orderBy('data_mart.klien_pengiriman')
        ->get();

        return view('period::setting-dp', $data);

    }

    public function viewDetail($code) {
        $data['periode'] = Periode::where('code', $code)->first();
        $data['sumber_waybill'] = DB::table($code.'.data_mart')->selectRaw('DISTINCT(sumber_waybill)')->orderBy('sumber_waybill')->pluck('sumber_waybill')->toArray();
        $data['klien_pengiriman'] = DB::table($code.'.data_mart'
        )->selectRaw('DISTINCT(data_mart.klien_pengiriman), COALESCE(master_klien_pengiriman_setting.is_reguler, 0) as is_reguler, COALESCE(master_klien_pengiriman_setting.is_dfod, 0) as is_dfod, COALESCE(master_klien_pengiriman_setting.is_super, 0) as is_super')
        ->leftJoin('master_klien_pengiriman_setting', function ($join) use ($data) {
            $join->on('master_klien_pengiriman_setting.klien_pengiriman', '=', 'data_mart.klien_pengiriman')
                ->where('master_klien_pengiriman_setting.periode_id', $data['periode']->id);
        })
        ->orderBy('data_mart.klien_pengiriman')
        ->get();

        // dd($data['klien_pengiriman'], $data['klien_pengiriman_kat'], array_($data['klien_pengiriman'], $data['klien_pengiriman_kat']));
        $data['row_total'] = DB::table($data['periode']->code.'.data_mart')->count();
        $data['cp_grade_a'] = DB::table('master_collection_point AS cp')->join($data['periode']->code.'.cp_dp_all_count_sum AS pivot', 'cp.drop_point_outgoing', '=', 'pivot.drop_point_outgoing')->select('cp.kode_cp', 'cp.nama_cp', 'pivot.count', 'pivot.sum')->where('cp.grading_pickup', 'A')->get();
        $data['cp_grade_b'] = DB::table('master_collection_point AS cp')->join($data['periode']->code.'.cp_dp_all_count_sum AS pivot', 'cp.drop_point_outgoing', '=', 'pivot.drop_point_outgoing')->select('cp.kode_cp', 'cp.nama_cp', 'pivot.count', 'pivot.sum')->where('cp.grading_pickup', 'B')->get();
        $data['cp_grade_c'] = DB::table('master_collection_point AS cp')->join($data['periode']->code.'.cp_dp_all_count_sum AS pivot', 'cp.drop_point_outgoing', '=', 'pivot.drop_point_outgoing')->select('cp.kode_cp', 'cp.nama_cp', 'pivot.count', 'pivot.sum')->where('cp.grading_pickup', 'C')->get();
        $data['cp_dp_mp_count_waybill'] = PivotTable::getPivotMPCountWaybill($code);
        $data['cp_dp_mp_sum_biaya_kirim'] = PivotTable::getPivotMPSumBiayaKirim($code);
        $data['cp_dp_mp_retur_count_waybill'] = PivotTable::getPivotMPReturCountWaybill($code);
        $data['cp_dp_mp_retur_sum_biaya_kirim'] = PivotTable::getPivotMPReturSumBiayaKirim($code);
        $data['cp_dp_all_count_sum'] = PivotTable::getPivotAllCountSumCPDP($code);
        $data['cp_dp_reguler_count_sum'] = PivotTable::getPivotRegulerCountSumCPDP($code);
        $data['cp_dp_dfod_count_sum'] = PivotTable::getPivotDfodCountSumCPDP($code);
        $data['cp_dp_super_count_sum'] = PivotTable::getPivotSuperCountSumCPDP($code);
        $data['dpf_mp_count_waybill'] = PivotTable::getPivotDPFMPCountWaybill($code);
        $data['dpf_mp_sum_biaya_kirim'] = PivotTable::getPivotDPFMPSumBiayaKirim($code);
        $data['dpf_mp_retur_count_waybill'] = PivotTable::getPivotDPFMPReturCountWaybill($code);
        $data['dpf_mp_retur_sum_biaya_kirim'] = PivotTable::getPivotDPFMPReturSumBiayaKirim($code);
        $data['dpf_all_count_sum'] = PivotTable::getPivotDPFAllCountSum($code);
        $data['dpf_reguler_count_sum'] = PivotTable::getPivotDPFRegulerCountSum($code);
        $data['dpf_dfod_count_sum'] = PivotTable::getPivotDPFDfodCountSum($code);
        $data['dpf_super_count_sum'] = PivotTable::getPivotDPFSuperCountSum($code);
        $data['sum_all_biaya_kirim'] = PivotTable::getSumAllBiayaKirim($code);
        $data['total'] = [
            'cp_dp_all_count_sum_total_count' => $data['cp_dp_all_count_sum']->sum('count'),
            'cp_dp_all_count_sum_total_sum' => $data['cp_dp_all_count_sum']->sum('sum'),
            'cp_dp_reguler_count_sum_total_count' => $data['cp_dp_reguler_count_sum']->sum('count'),
            'cp_dp_reguler_count_sum_total_sum' => $data['cp_dp_reguler_count_sum']->sum('sum'),
            'cp_dp_super_count_sum_total_count' => $data['cp_dp_super_count_sum']->sum('count'),
            'cp_dp_super_count_sum_total_sum' => $data['cp_dp_super_count_sum']->sum('sum'),
            'cp_dp_dfod_count_sum_total_count' => $data['cp_dp_dfod_count_sum']->sum('count'),
            'cp_dp_dfod_count_sum_total_sum' => $data['cp_dp_dfod_count_sum']->sum('sum'),
            'dpf_all_count_sum_total_count' => $data['dpf_all_count_sum']->sum('count'),
            'dpf_all_count_sum_total_sum' => $data['dpf_all_count_sum']->sum('sum'),
            'dpf_reguler_count_sum_total_count' => $data['dpf_reguler_count_sum']->sum('count'),
            'dpf_reguler_count_sum_total_sum' => $data['dpf_reguler_count_sum']->sum('sum'),
            'dpf_super_count_sum_total_count' => $data['dpf_super_count_sum']->sum('count'),
            'dpf_super_count_sum_total_sum' => $data['dpf_super_count_sum']->sum('sum'),
            'dpf_dfod_count_sum_total_count' => $data['dpf_dfod_count_sum']->sum('count'),
            'dpf_dfod_count_sum_total_sum' => $data['dpf_dfod_count_sum']->sum('sum'),
            'grade_a_summary_total_count' => $data['cp_grade_a']->sum('count'),
            'grade_a_summary_total_sum' => $data['cp_grade_a']->sum('sum'),
            'grade_b_summary_total_count' => $data['cp_grade_b']->sum('count'),
            'grade_b_summary_total_sum' => $data['cp_grade_b']->sum('sum'),
            'grade_c_summary_total_count' => $data['cp_grade_c']->sum('count'),
            'grade_c_summary_total_sum' => $data['cp_grade_c']->sum('sum'),
        ];
        $data['log_resi'] = LogResi::where('periode_id', $data['periode']->id)->get();
        return view('period::summary-periode', $data);
    }

    public function updateSettingDP(Request $request, $id) {
        $periode = Periode::where('id', $id)->first();
        foreach($request['dp'] as $item) {
            if(intval($item['is_import'])) {
                $exist = SettingDpPeriode::where(['periode_id' => $id, 'drop_point_outgoing' => $item['drop_point_outgoing']])->first();

                if($exist) {
                    $exist->update([
                        "pengurangan_total" => intval($item['pengurangan_total']),
                        "penambahan_total" => intval($item['penambahan_total']),
                        "diskon_cod" => intval($item['diskon_cod']),
                    ]);
                } else {
                    SettingDpPeriode::create([
                        'periode_id' => $id,
                        'drop_point_outgoing' => $item['drop_point_outgoing'],
                        "pengurangan_total" => intval($item['pengurangan_total']),
                        "penambahan_total" => intval($item['penambahan_total']),
                        "diskon_cod" => intval($item['diskon_cod']),
                    ]);
                }
            }

        }

        //update schema->cp_dp_cashback_reguler_grading_1 , cp_dp_cashback_non_cod_grading_1 -> update pengurangan_total,
        //penambahan_total ->
        CreateSchema::runUpdateDiskonPenguranganPenambahan($periode->code, $id);

        toastr()->success('Data setting klien pengiriman succesfully saved', 'Success');
        return redirect()->back();
    }

    public function updateKlien(Request $request, $id) {
        $periode = Periode::where('id', $id)->first();

        $is_reguler = [];
        $is_dfod = [];
        $is_super = [];

        foreach($request['klien'] as $item) {
            $exist = KlienPengiriman::where(['periode_id' => $id, 'klien_pengiriman' => $item['item']])->first();

            if($exist) {
                $exist->update([
                    'is_reguler' => $item['reguler'],
                    'is_dfod' => $item['dfod'],
                    'is_super' => $item['super'],
                ]);
            } else {
                KlienPengiriman::create([
                    'periode_id' => $id,
                    'klien_pengiriman' => $item['item'],
                    'is_reguler' => intval($item['reguler']),
                    'is_dfod' => intval($item['dfod']),
                    'is_super' => intval($item['super'])
                ]);
            }

            if(intval($item['reguler'])) {
                $is_reguler[] = "'".$item['item']."'";
            }

            if (intval($item['dfod'])) {
                $is_dfod[] = "'".$item['item']."'";
            }

            if (intval($item['super'])) {
                $is_super[] = "'".$item['item']."'";
            }
        }

        $string['reguler'] = implode(',', $is_reguler);
        $string['dfod'] = implode(',', $is_dfod);
        $string['super'] = implode(',', $is_super);

        //update pivot here

        CreateSchema::updateViewPivot($periode->code, $string);

        toastr()->success('Data setting klien pengiriman succesfully saved', 'Success');
        return redirect()->back();
    }
}
