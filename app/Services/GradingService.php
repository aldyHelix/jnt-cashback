<?php
namespace App\Services;

use App\Exports\CashbackGrading1Export;
use App\Exports\CashbackGrading2Export;
use App\Exports\CashbackGrading3Export;
use App\Exports\CashbackGradingDeliveryExport;
use App\Exports\GradingExport;
use App\Models\Periode;
use App\Models\PeriodeDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GradingService {
    public function generateGradingDelivery($id, $code){
        $get_periode = PeriodeDelivery::findOrFail($id);
        $schema = 'delivery_'.strtolower($get_periode->month).'_'.$get_periode->year;

        $data['delivery_fee_summary'] = DB::table($schema.'.delivery_fee_summary')->get()->toArray();

        if($data['delivery_fee_summary'] == []){
            return toastr()->error('Data not generated, please check data.', 'Error');
        }

        $data['rekap_denda_delivery_fee_summary'] = DB::table($schema.'.rekap_denda_delivery_fee_summary')->get()->toArray();

        $this->exportFileGradeDelivery($data, $get_periode->month, $get_periode->year);
    }

    public function generateGrading($id, $grade) {
        // Store on default disk
        $get_periode = Periode::where('id',$id)->with('jsonData')->first();
        $schema = $get_periode->code;

        switch ($grade) {
            case 1:
                $header_cpdp_reguler = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'biaya_kirim_all' => 'Total Biaya Kirim Keseluruhan',
                    'biaya_kirim_reguler' => 'Biaya Kirim Reguler',
                    'biaya_kirim_dfod' => 'Biaya Kirim DFOD',
                    'biaya_kirim_super' => 'Biaya Kirim Super',
                    'total_biaya_kirim' => 'Total Biaya Kirim',
                    'total_biaya_kirim_dikurangi_ppn' => 'Total Biaya Kirim Dikurangi PPN',
                    'amount_discount_25' => 'Amount Diskon 25%',
                    'total_cashback_reguler' => 'Total Cashback Reguler',
                ];
                $data['cpdp_reguler'] = json_decode($get_periode->jsonData->cashback_reguler);
                array_unshift($data['cpdp_reguler'], $header_cpdp_reguler);

                $header_cpdp_cod = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'bukalapak' => 'BUKALAPAK',
                    'total_biaya_kirim_bukalapak' => 'Biaya Kirim BUKALAPAK',
                    'biaya_kirim_bukalapak_dikurangi_ppn' => 'Biaya Kirim BUKALAPAK Dikurangi PPN',
                    'discount_bukalapak_7' => 'DISCOUNT BUKALAPAK 7%',
                    'tokopedia' => 'TOKOPEDIA',
                    'tokopedia_reguler' => 'TOKOPEDIA REGULER',
                    'total_biaya_kirim_tokopedia' => 'Total Biaya Kirim TOKOPEDIA',
                    'total_biaya_kirim_tokopedia_dikurangi_ppn' => 'Total Biaya Kirim TOKOPEDIA dikurangi PPN',
                    'diskon_tokopedia_7' => 'Discount TOKOPEDIA 7%',
                    'total_biaya_kirim_bukalapak_tokopedia' => 'Total Biaya Kirim TOKOPEDIA , BUKALAPAK',
                    'shopee_cod' => 'SHOPEE COD',
                    'retur_shopee_cod' => 'Return SHOPEE COD',
                    'total_biaya_kirim_shopee_cod' => 'Total Biaya Kirim SHOPEE COD',
                    'magellan_cod' => 'MAGELLAN COD',
                    'retur_magellan_cod' => 'Retur MAGELLAN COD',
                    'total_biaya_kirim_magellan_cod' => 'Total Biaya Kirim Magellan COD',
                    'lazada_cod' => 'LAZADA COD',
                    'retur_lazada_cod' => 'Retur LAZADA COD',
                    'total_biaya_kirim_lazada_cod' => 'Total Biaya Kirim LAZADA COD',
                    'total_biaya_kirim_cod' => 'Total Biaya Kirim COD',
                    'total_biaya_kirim_cod_dikurangi_ppn' => 'Total Biaya Kirim COD Dikurangi PPN',
                    'diskon_cod_7' => 'Discount COD 7%',
                    'cashback_marketplace' => 'Cashback Marketplace',
                ];
                $data['cpdp_cod'] = json_decode($get_periode->jsonData->cashback_marketplace_cod);
                array_unshift($data['cpdp_cod'], $header_cpdp_cod);


                $header_cpdp_non_cod = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'lazada' => 'LAZADA',
                    'retur_lazada' => 'Retur LAZADA',
                    'shopee' => 'SHOPEE',
                    'retur_shopee' => 'Retur SHOPEE',
                    'magellan' => 'MAGELLAN',
                    'retur_magellan' => 'Retur MAGELLAN',
                    'total_biaya_kirim_non_cod' => 'Total Biaya Kirim NON COD',
                    'total_retur_pilihan' => 'Total Retur Pilihan',
                    'retur_belum_terpotong' => 'Retur Belum Terpotong',
                    'total_biaya_kirim_marketplace' => 'Total Biaya Kirim Marketplace',
                    'total_biaya_kirim_non_cod_dikurangi_ppn' => 'Total Biaya Kirim Non COD Dikurangi PPN',
                    'discount_total_biaya_kirim_7' => 'Discount Total Biaya Kirim 7%',
                    'total_cashback_marketplace' => 'Total Cashback Marketplace',
                ];
                $data['cpdp_non_cod'] = json_decode($get_periode->jsonData->cashback_marketplace_non_cod);
                array_unshift($data['cpdp_non_cod'], $header_cpdp_non_cod);

                $header_cpdp_vip = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'akulakuob' => 'AKULAKUOB',
                    'ordivo' => 'ORDIVO',
                    'evermosapi' => 'EVERMOSAPI',
                    'mengantar' => 'MENGANTAR',
                    'klien_pengirim_vip' => 'KLIEN PENGIRIM VIP',
                    'total_biaya_kirim_vip' => 'Total Biaya Kirim VIP',
                    'total_biaya_kirim_vip_dikurangi_ppn' => 'Total Biaya Kirim VIP dikurangi ppn',
                    'discount_total_biaya_kirim_10' => 'Total Cashback Klien VIP',
                ];
                $data['cpdp_vip'] = json_decode($get_periode->jsonData->cashback_klien_vip);
                array_unshift($data['cpdp_vip'], $header_cpdp_vip);


                $header_cpdp_rekap_grading_1 = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'total_biaya_kirim_reguler' => 'Total Biaya Kirim Reguler',
                    'total_cashback_marketplace_non_cod' => 'Total Cashback marketplace',
                    'total_cashback_mp_luar_zona' => 'Total Cashback Luar Zona',
                    'total_cashback_vip' => 'Total Cashback VIP',
                    'total_cashback' => 'Total Cashback',
                ];
                $data['cpdp_rekap_grading_1'] = json_decode($get_periode->jsonData->cashback_grading_1);
                array_unshift($data['cpdp_rekap_grading_1'], $header_cpdp_rekap_grading_1 );

                $header_cpdp_rekap_klien_vip = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'akulakuob' => 'AKULAKUOB',
                    'arveoli' => 'ARVEOLI',
                    'biteship' => 'BITESHIP',
                    'blibliapi' => 'BLIBLIAPI',
                    'brttrimentari' => 'BRTTRIMENTARI',
                    'clodeohq' => 'CLODEOHQ',
                    'coogee_hq_vip' => 'COOGEE-HQ-VIP',
                    'destyapi' => 'DESTYAPI',
                    'doctorship' => 'DOCTORSHIP',
                    'donatelloindo' => 'DONATELLOINDO',
                    'evermosapi' => 'EVERMOSAPI',
                    'goapotik' => 'GOAPOTIK',
                    'gramedia' => 'GRAMEDIA',
                    'istyleid' => 'ISTYLEID',
                    'kkid' => 'KKID',
                    'mengantar' => 'MENGANTAR',
                    'ordivo' => 'ORDIVO',
                    'parama' => 'PARAMA',
                    'plugo' => 'PLUGO',
                    'returnkey' => 'RETURNKEY',
                    'sepasangcollection' => 'SEPASANGCOLLECTION',
                    'shipperid' => 'SHIPPERID',
                    'sirclostore' => 'SIRCLOSTORE',
                    'tries' => 'TRIES',
                    'grand_total' => 'Grand Total',
                    'klien_pengirim_vip' => 'Total VIP Sumber waybill',
                ];
                $data['cpdp_rekap_klien_vip'] = json_decode($get_periode->jsonData->pivot_vip);
                array_unshift($data['cpdp_rekap_klien_vip'], $header_cpdp_rekap_klien_vip );

                $header_cpdp_rekap_denda = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'nama_pt' => 'Nama PT',
                    'total_cashback' => 'Total Cashback',
                    'penambahan_total' => 'Penambahan Total',
                    'total_penambahan_total' => 'Total Penambahan Cashback',
                    'transit_fee' => 'Transit Fee',
                    'total_cashback_dikurangi_transit_fee' => 'Total Cashback Dikurangi Transit Fee',
                    'denda_void' => 'Denda Void',
                    'denda_dfod' => 'Denda Dfod',
                    'denda_pusat' => 'Denda Pusat',
                    'denda_selisih_berat' => 'Denda Selisih Berat',
                    'denda_lost_scan_kirim' => 'Denda Lost Scan Kirim',
                    'denda_auto_claim' => 'Denda Auto Claim',
                    'denda_sposorship' => 'Denda Sponsorship',
                    'denda_late_pickup_ecommerce' => 'Denda Late Pickup Ecommerce',
                    'potongan_pop' => 'Potongan POP',
                    'denda_lainnya' => 'Denda Lainnya',
                    'total_denda' => 'Total Denda',
                    'pengurangan_total' => 'Pengurangan Total',
                    'total_pengurangan_cashback' => 'Total Pengurangan Cashback',
                    'total_cashback_setelah_pengurangan' => 'Total Cashback Setelah Pengurangan',
                    'dpp' => 'DPP',
                    'pph' => 'PPH',
                    'amount_pph_2' => 'AMOUNT PPH 2%',
                    'amount_setelah_pph' => 'Total Cashback setelah PPH',
                    'nama_bank' => 'Nama Bank',
                    'nomor_rekening' => 'Nomor Rekening',
                ];
                $data['cpdp_rekap_denda'] = json_decode($get_periode->jsonData->cashback_grading_1_denda);
                array_unshift($data['cpdp_rekap_denda'], $header_cpdp_rekap_denda);

                $this->exportFileGrade1($data, $get_periode->month, $get_periode->year);
                break;
            case 2:
                $data['cpdp_reguler'] = DB::table($schema.'.cp_dp_cashback_reguler_grading_2')->get()->toArray();
                $data['cpdp_cod'] = DB::table($schema.'.cp_dp_cashback_awb_grading_2')->get()->toArray();
                $data['cpdp_rekap'] = DB::table($schema.'.cp_dp_rekap_cashback_grading_2')->get()->toArray();
                $data['cpdp_rekap_denda'] = DB::table($schema.'.cp_dp_rekap_denda_cashback_grading_2')->get()->toArray();

                $this->exportFileGrade2($data, $get_periode->month, $get_periode->year);
                break;
            case 3:
                $data['cpdp_reguler'] = DB::table($schema.'.cp_dp_cashback_reguler_grading_3')->get()->toArray();
                $data['cpdp_cod'] = DB::table($schema.'.cp_dp_cashback_awb_grading_3')->get()->toArray();
                $data['cpdp_rekap'] = DB::table($schema.'.cp_dp_rekap_cashback_grading_3')->get()->toArray();
                $data['cpdp_rekap_denda'] = DB::table($schema.'.cp_dp_rekap_denda_cashback_grading_3')->get()->toArray();

                $this->exportFileGrade3($data, $get_periode->month, $get_periode->year);
                break;
            case 'deelivery':

                break;
            default:
                # code...
                break;
        }


        return true;
    }

    public function exportFileGradeDelivery($data ,$month, $year) {
        $file_name = strtoupper($month).'-'.$year.'-DELIVERY.xlsx';

        $storage_exist = storage_path($file_name);

        if (file_exists($storage_exist)) {
            // The file exists in the storage directory.
            // You can perform further actions here.
            unlink($storage_exist); # delete old file before create new one with same name
            Storage::delete($file_name);
        }

        $gradingExport = new CashbackGradingDeliveryExport($data, $file_name);

        Excel::store($gradingExport, $file_name, 'public');//this is success

        // Append the sum row after storing the Excel file
    }

    public function exportFileGrade1($data ,$month, $year) {
        $file_name = strtoupper($month).'-'.$year.'-GRADING-1.xlsx';

        $storage_exist = storage_path($file_name);

        if (file_exists($storage_exist)) {
            // The file exists in the storage directory.
            // You can perform further actions here.
            unlink($storage_exist); # delete old file before create new one with same name
            Storage::delete($file_name);
        }

        $gradingExport = new CashbackGrading1Export($data, $file_name);

        Excel::store($gradingExport, $file_name, 'public');//this is success

        // Append the sum row after storing the Excel file
    }

    public function exportFileGrade2($periode_code ,$month, $year) {
        $file_name = strtoupper($month).'-'.$year.'-GRADING-2.xlsx';

        $storage_exist = storage_path($file_name);

        if (file_exists($storage_exist)) {
            // The file exists in the storage directory.
            // You can perform further actions here.
            unlink($storage_exist); # delete old file before create new one with same name
            Storage::delete($file_name);
        }

        $gradingExport = new CashbackGrading2Export($periode_code, $file_name);

        Excel::store($gradingExport, $file_name, 'public');//this is success

        // Append the sum row after storing the Excel file
    }

    public function exportFileGrade3($periode_code ,$month, $year) {
        $file_name = strtoupper($month).'-'.$year.'-GRADING-3.xlsx';

        $storage_exist = storage_path($file_name);

        if (file_exists($storage_exist)) {
            // The file exists in the storage directory.
            // You can perform further actions here.
            unlink($storage_exist); # delete old file before create new one with same name
            Storage::delete($file_name);
        }

        $gradingExport = new CashbackGrading3Export($periode_code, $file_name);

        Excel::store($gradingExport, $file_name, 'public');//this is success

        // Append the sum row after storing the Excel file
    }
}
