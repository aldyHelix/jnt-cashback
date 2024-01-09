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
                    'marketplace_reguler' => 'Marketplace Reguler',
                    'biaya_kirim_reguler' => 'Biaya Kirim Reguler',
                    'biaya_kirim_dfod' => 'Biaya Kirim DFOD',
                    'biaya_kirim_super' => 'Biaya Kirim Super',
                    'total_biaya_kirim' => 'Total Biaya Kirim',
                    'total_biaya_kirim_dikurangi_ppn' => 'Total Biaya Kirim Dikurangi PPN',
                    'amount_discount_25' => 'Amount Diskon',
                    'biaya_kirim_vip' => 'Biaya Kirim VIP',
                    'total_biaya_vip_setelah_ppn' => 'Biaya Kirim Setelah PPN',
                    'total_biaya_vip_ppn_diskon' => 'Biaya Kirim Setelah Diskon',
                    'total_cashback_reguler' => 'Total Cashback Reguler',
                ];
                $data['cpdp_reguler_a'] = json_decode($get_periode->jsonData->cashback_reguler_a);
                array_unshift($data['cpdp_reguler_a'], $header_cpdp_reguler);

                $header_cpdp_cod = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'bukalapak' => 'BUKALAPAK',
                    'diskon_platform_bukalapak' => 'Diskon Platform',
                    'total_setelah_diskon_bukalapak' => 'Biaya Kirim BUKALAPAK',
                    'tokopedia' => 'TOKOPEDIA',
                    'tokopedia_reguler' => 'TOKOPEDIA REGULER',
                    'total_biaya_kirim_tokopedia' => 'Biaya Kirim TOKOPEDIA',
                    'diskon_platform_tokopedia' => 'Diskon Platform',
                    'total_setelah_diskon_tokopedia' => 'Total Biaya Kirim TOKOPEDIA',
                    'lazada_all' => 'LAZADA ALL',
                    'lazada_retur_all' => 'Retur LAZADA ALL',
                    'total_biaya_kirim_lazada_cod' => 'Biaya Kirim LAZADA',
                    'diskon_platform_lazada' => 'Diskon Platform',
                    'total_setelah_diskon_lazada' => 'Total Biaya Kirim LAZADA ',
                    'magellan_all' => 'MAGELLAN ALL',
                    'megallan_retur_all' => 'Retur MAGELLAN ALL',
                    'shopee_all' => 'SHOPEE ALL',
                    'shopee_retur_all' => 'Return SHOPEE ALL',
                    'total_biaya_kirim_shopee_magellan' => 'Total Biaya Kirim Magellan Shopee',
                    'diskon_platform_shopee_magellan' => 'Diskon Platform',
                    'total_setelah_diskon_shopee_magellan' => 'Total setelah Diskon Pusat',
                    'retur_lain' => 'Retur Bukalapak, Tokopedia, dll',
                    'retur_belum_terpotong' => 'Retur belum terpotong',
                    'total_setelah_diskon_pusat' => 'Total setelah diskon pusat',
                    'total_biaya_kirim_dikurangi_ppn' => 'Total Biaya Kirim Dikurangi PPN',
                    'diskon_marketplace' => 'Diskon Marketplace',
                    'cashback_marketplace' => 'Cashback Marketplace',
                ];
                $data['cpdp_cod'] = json_decode($get_periode->jsonData->cashback_marketplace_cod);
                array_unshift($data['cpdp_cod'], $header_cpdp_cod);


                // $header_cpdp_non_cod = (object) [
                //     'kode_cp' => 'Kode CP',
                //     'nama_cp' => 'Nama CP',
                //     'lazada' => 'LAZADA',
                //     'retur_lazada' => 'Retur LAZADA',
                //     'shopee' => 'SHOPEE',
                //     'retur_shopee' => 'Retur SHOPEE',
                //     'magellan' => 'MAGELLAN',
                //     'retur_magellan' => 'Retur MAGELLAN',
                //     'total_biaya_kirim_non_cod' => 'Total Biaya Kirim NON COD',
                //     'total_retur_pilihan' => 'Total Retur Pilihan',
                //     'retur_belum_terpotong' => 'Retur Belum Terpotong',
                //     'total_biaya_kirim_marketplace' => 'Total Biaya Kirim Marketplace',
                //     'total_biaya_kirim_non_cod_dikurangi_ppn' => 'Total Biaya Kirim Non COD Dikurangi PPN',
                //     'discount_total_biaya_kirim_7' => 'Discount Total Biaya Kirim 7%',
                //     'total_cashback_marketplace' => 'Total Cashback Marketplace',
                // ];
                // $data['cpdp_non_cod'] = json_decode($get_periode->jsonData->cashback_marketplace_non_cod);
                // array_unshift($data['cpdp_non_cod'], $header_cpdp_non_cod);

                // $header_cpdp_vip = (object) [
                //     'kode_cp' => 'Kode CP',
                //     'nama_cp' => 'Nama CP',
                //     'akulakuob' => 'AKULAKUOB',
                //     'ordivo' => 'ORDIVO',
                //     'evermosapi' => 'EVERMOSAPI',
                //     'mengantar' => 'MENGANTAR',
                //     'klien_pengirim_vip' => 'KLIEN PENGIRIM VIP',
                //     'total_biaya_kirim_vip' => 'Total Biaya Kirim VIP',
                //     'total_biaya_kirim_vip_dikurangi_ppn' => 'Total Biaya Kirim VIP dikurangi ppn',
                //     'discount_total_biaya_kirim_10' => 'Total Cashback Klien VIP',
                // ];
                // $data['cpdp_vip'] = json_decode($get_periode->jsonData->cashback_klien_vip);
                // array_unshift($data['cpdp_vip'], $header_cpdp_vip);


                $header_cpdp_rekap_grading_1 = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'total_cashback_reguler' => 'Total Biaya Kirim Reguler',
                    'total_cashback_marketplace' => 'Total Cashback marketplace',
                    'total_cashback_mp_luar_zona' => 'Total Cashback Luar Zona',
                    'total_cashback' => 'Total Cashback',
                ];
                $data['cpdp_rekap_grading_1'] = json_decode($get_periode->jsonData->cashback_grading_1);
                array_unshift($data['cpdp_rekap_grading_1'], $header_cpdp_rekap_grading_1 );

                // $header_cpdp_rekap_klien_vip = (object) [
                //     'kode_cp' => 'Kode CP',
                //     'nama_cp' => 'Nama CP',
                //     'akulakuob' => 'AKULAKUOB',
                //     'arveoli' => 'ARVEOLI',
                //     'biteship' => 'BITESHIP',
                //     'blibliapi' => 'BLIBLIAPI',
                //     'brttrimentari' => 'BRTTRIMENTARI',
                //     'clodeohq' => 'CLODEOHQ',
                //     'coogee_hq_vip' => 'COOGEE-HQ-VIP',
                //     'destyapi' => 'DESTYAPI',
                //     'doctorship' => 'DOCTORSHIP',
                //     'donatelloindo' => 'DONATELLOINDO',
                //     'evermosapi' => 'EVERMOSAPI',
                //     'goapotik' => 'GOAPOTIK',
                //     'gramedia' => 'GRAMEDIA',
                //     'istyleid' => 'ISTYLEID',
                //     'kkid' => 'KKID',
                //     'mengantar' => 'MENGANTAR',
                //     'ordivo' => 'ORDIVO',
                //     'parama' => 'PARAMA',
                //     'plugo' => 'PLUGO',
                //     'returnkey' => 'RETURNKEY',
                //     'sepasangcollection' => 'SEPASANGCOLLECTION',
                //     'shipperid' => 'SHIPPERID',
                //     'sirclostore' => 'SIRCLOSTORE',
                //     'tries' => 'TRIES',
                //     'grand_total' => 'Grand Total',
                //     'klien_pengirim_vip' => 'Total VIP Sumber waybill',
                // ];
                // $data['cpdp_rekap_klien_vip'] = json_decode($get_periode->jsonData->pivot_vip);
                // array_unshift($data['cpdp_rekap_klien_vip'], $header_cpdp_rekap_klien_vip );

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
                $header_cpdp_reguler = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'biaya_kirim_reguler' => 'Biaya Kirim Reguler',
                    'biaya_kirim_dfod' => 'Biaya Kirim DFOD',
                    'biaya_kirim_super' => 'Biaya Kirim Super',
                    'marketplace_reguler' => 'Marketplace Reguler',
                    'total_biaya_kirim' => 'Total Biaya Kirim',
                    'total_biaya_kirim_dikurangi_ppn' => 'Total Biaya Kirim Dikurangi PPN',
                    'amount_discount_25' => 'Amount Diskon',
                    'total_cashback_reguler' => 'Total Cashback Reguler',
                ];
                $data['cpdp_reguler_b'] = json_decode($get_periode->jsonData->cashback_reguler_b);
                array_unshift($data['cpdp_reguler_b'], $header_cpdp_reguler);

                $header_cpdp_cod = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'bukalapak' => 'AWB BUKALAPAK',
                    'shopee' => 'AWB SHOPEE',
                    'lazada' => 'AWB LAZADA',
                    'tokopedia' => 'AWB TOKOPEDIA',
                    'magellan' => 'AWB MAGELLAN',
                    'klien_pengirim_vip' => 'KLIEN PENGIRIM VIP',
                    'retur_shopee' => 'AWB Retur SHOPEE',
                    'retur_magellan' => 'AWB Retur MAGELLAN',
                    'retur_pilihan' => 'AWB Retur LAZADA, AKULAKU, TOKOPEDIA,DLL',
                    'retur_belum_terpotong' => 'Retur Belum Terpotong',
                    'total_awb' => 'TOTAL AWB',
                    'discount_per_awb' => 'Discount 750@AWB',
                    'ppn' => 'PPN',
                    'total_cashback_marketplace' => 'TOTAL CASHBACK MARKETPLACE',
                ];
                $data['cpdp_cod'] = json_decode($get_periode->jsonData->cashback_marketplace_awb_cod);
                array_unshift($data['cpdp_cod'], $header_cpdp_cod);

                $header_cpdp_cashback = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'total_cashback_reguler' => 'Total Cashback Reguler',
                    'total_cashback_marketplace_non_cod' => 'Total Cashback Marketplace',
                    'total_cashback' => 'Total Cashback',
                ];
                $data['cpdp_rekap_grading_2'] = json_decode($get_periode->jsonData->cashback_grading_2);
                array_unshift($data['cpdp_rekap_grading_2'], $header_cpdp_cashback);

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
                $data['cpdp_rekap_denda'] = json_decode($get_periode->jsonData->cashback_grading_2_denda);
                array_unshift($data['cpdp_rekap_denda'], $header_cpdp_rekap_denda);

                $this->exportFileGrade2($data, $get_periode->month, $get_periode->year);
                break;
            case 3:

                $header_cpdp_reguler = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'biaya_kirim_reguler' => 'Biaya Kirim Reguler',
                    'marketplace_reguler' => 'Marketplace Reguler',
                    'biaya_kirim_dfod' => 'Biaya Kirim DFOD',
                    'biaya_kirim_super' => 'Biaya Kirim Super',
                    'total_biaya_kirim' => 'Total Biaya Kirim',
                    'total_biaya_kirim_dikurangi_ppn' => 'Total Biaya Kirim Dikurangi PPN',
                    'amount_discount_20' => 'Amount Diskon',
                    'total_cashback_reguler' => 'Total Cashback Reguler',
                ];
                $data['cpdp_reguler_c'] = json_decode($get_periode->jsonData->cashback_reguler_c);
                array_unshift($data['cpdp_reguler_c'], $header_cpdp_reguler);

                $header_cpdp_cod = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'bukalapak' => 'AWB BUKALAPAK',
                    'shopee' => 'AWB SHOPEE',
                    'lazada' => 'AWB LAZADA',
                    'tokopedia' => 'AWB TOKOPEDIA',
                    'magellan' => 'AWB MAGELLAN',
                    'klien_pengirim_vip' => 'KLIEN PENGIRIM VIP',
                    'retur_shopee' => 'AWB Retur SHOPEE',
                    'retur_magellan' => 'AWB Retur MAGELLAN',
                    'retur_pilihan' => 'AWB Retur LAZADA, AKULAKU, TOKOPEDIA,DLL',
                    'retur_belum_terpotong' => 'Retur Belum Terpotong',
                    'total_awb' => 'TOTAL AWB',
                    'discount_per_awb' => 'Discount 750@AWB',
                    'ppn' => 'PPN',
                    'total_cashback_marketplace' => 'TOTAL CASHBACK MARKETPLACE',
                ];
                $data['cpdp_cod'] = json_decode($get_periode->jsonData->cashback_marketplace_awb_g3_cod);
                array_unshift($data['cpdp_cod'], $header_cpdp_cod);

                $header_cpdp_cashback = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'total_cashback_reguler' => 'Total Cashback Reguler',
                    'total_cashback_marketplace_non_cod' => 'Total Cashback Marketplace',
                    'total_cashback' => 'Total Cashback',
                ];
                $data['cpdp_rekap_grading_3'] = json_decode($get_periode->jsonData->cashback_grading_3);
                array_unshift($data['cpdp_rekap_grading_3'], $header_cpdp_cashback);

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
                $data['cpdp_rekap_denda'] = json_decode($get_periode->jsonData->cashback_grading_3_denda);
                array_unshift($data['cpdp_rekap_denda'], $header_cpdp_rekap_denda);

                $this->exportFileGrade3($data, $get_periode->month, $get_periode->year);
                break;
            case 'dpf':
                $header_dpf_reguler = (object) [
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
                $data['dpf_reguler'] = json_decode($get_periode->jsonData->dpf_cashback_reguler);
                array_unshift($data['dpf_reguler'], $header_dpf_reguler);

                $header_dpf_cod = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'bukalapak' => 'BUKALAPAK',
                    'diskon_platform_bukalapak' => 'Diskon Platform',
                    'total_setelah_diskon_bukalapak' => 'Biaya Kirim BUKALAPAK',
                    'tokopedia' => 'TOKOPEDIA',
                    'tokopedia_reguler' => 'TOKOPEDIA REGULER',
                    'total_biaya_kirim_tokopedia' => 'Biaya Kirim TOKOPEDIA',
                    'diskon_platform_tokopedia' => 'Diskon Platform',
                    'total_setelah_diskon_tokopedia' => 'Total Biaya Kirim TOKOPEDIA',
                    'lazada_all' => 'LAZADA ALL',
                    'lazada_retur_all' => 'Retur LAZADA ALL',
                    'total_biaya_kirim_lazada_cod' => 'Biaya Kirim LAZADA',
                    'diskon_platform_lazada' => 'Diskon Platform',
                    'total_setelah_diskon_lazada' => 'Total Biaya Kirim LAZADA ',
                    'magellan_all' => 'MAGELLAN ALL',
                    'megallan_retur_all' => 'Retur MAGELLAN ALL',
                    'shopee_all' => 'SHOPEE ALL',
                    'shopee_retur_all' => 'Return SHOPEE ALL',
                    'total_biaya_kirim_shopee_magellan' => 'Total Biaya Kirim Magellan Shopee',
                    'diskon_platform_shopee_magellan' => 'Diskon Platform',
                    'retur_lain' => 'Retur Bukalapak, Tokopedia, dll',
                    'retur_belum_terpotong' => 'Retur belum terpotong',
                    'total_setelah_diskon_pusat' => 'Total setelah diskon pusat',
                    'total_biaya_kirim_dikurangi_ppn' => 'Total Biaya Kirim Dikurangi PPN',
                    'diskon_marketplace' => 'Diskon Marketplace',
                    'cashback_marketplace' => 'Cashback Marketplace',
                ];
                $data['dpf_cod'] = json_decode($get_periode->jsonData->dpf_cashback_marketplace_cod);
                array_unshift($data['dpf_cod'], $header_dpf_cod);


                // $header_dpf_non_cod = (object) [
                //     'kode_cp' => 'Kode CP',
                //     'nama_cp' => 'Nama CP',
                //     'lazada' => 'LAZADA',
                //     'retur_lazada' => 'Retur LAZADA',
                //     'shopee' => 'SHOPEE',
                //     'retur_shopee' => 'Retur SHOPEE',
                //     'magellan' => 'MAGELLAN',
                //     'retur_magellan' => 'Retur MAGELLAN',
                //     'total_biaya_kirim_non_cod' => 'Total Biaya Kirim NON COD',
                //     'total_retur_pilihan' => 'Total Retur Pilihan',
                //     'retur_belum_terpotong' => 'Retur Belum Terpotong',
                //     'total_biaya_kirim_marketplace' => 'Total Biaya Kirim Marketplace',
                //     'total_biaya_kirim_non_cod_dikurangi_ppn' => 'Total Biaya Kirim Non COD Dikurangi PPN',
                //     'discount_total_biaya_kirim_7' => 'Discount Total Biaya Kirim 7%',
                //     'total_cashback_marketplace' => 'Total Cashback Marketplace',
                // ];
                // $data['dpf_non_cod'] = json_decode($get_periode->jsonData->dpf_cashback_marketplace_non_cod);
                // array_unshift($data['dpf_non_cod'], $header_dpf_non_cod);

                // $header_dpf_vip = (object) [
                //     'kode_cp' => 'Kode CP',
                //     'nama_cp' => 'Nama CP',
                //     'akulakuob' => 'AKULAKUOB',
                //     'ordivo' => 'ORDIVO',
                //     'evermosapi' => 'EVERMOSAPI',
                //     'mengantar' => 'MENGANTAR',
                //     'klien_pengirim_vip' => 'KLIEN PENGIRIM VIP',
                //     'total_biaya_kirim_vip' => 'Total Biaya Kirim VIP',
                //     'total_biaya_kirim_vip_dikurangi_ppn' => 'Total Biaya Kirim VIP dikurangi ppn',
                //     'discount_total_biaya_kirim_10' => 'Total Cashback Klien VIP',
                // ];
                // $data['dpf_vip'] = json_decode($get_periode->jsonData->dpf_cashback_klien_vip);
                // array_unshift($data['dpf_vip'], $header_dpf_vip);


                $header_dpf_rekap_grading_1 = (object) [
                    'kode_cp' => 'Kode CP',
                    'nama_cp' => 'Nama CP',
                    'total_cashback_reguler' => 'Total Biaya Kirim Reguler',
                    'total_cashback_marketplace' => 'Total Cashback marketplace',
                    'total_cashback_mp_luar_zona' => 'Total Cashback Luar Zona',
                    'total_cashback' => 'Total Cashback',
                ];
                $data['dpf_rekap_cashback'] = json_decode($get_periode->jsonData->dpf_cashback_rekap);
                array_unshift($data['dpf_rekap_cashback'], $header_dpf_rekap_grading_1 );

                // $header_dpf_rekap_klien_vip = (object) [
                //     'kode_cp' => 'Kode CP',
                //     'nama_cp' => 'Nama CP',
                //     'akulakuob' => 'AKULAKUOB',
                //     'arveoli' => 'ARVEOLI',
                //     'biteship' => 'BITESHIP',
                //     'blibliapi' => 'BLIBLIAPI',
                //     'brttrimentari' => 'BRTTRIMENTARI',
                //     'clodeohq' => 'CLODEOHQ',
                //     'coogee_hq_vip' => 'COOGEE-HQ-VIP',
                //     'destyapi' => 'DESTYAPI',
                //     'doctorship' => 'DOCTORSHIP',
                //     'donatelloindo' => 'DONATELLOINDO',
                //     'evermosapi' => 'EVERMOSAPI',
                //     'goapotik' => 'GOAPOTIK',
                //     'gramedia' => 'GRAMEDIA',
                //     'istyleid' => 'ISTYLEID',
                //     'kkid' => 'KKID',
                //     'mengantar' => 'MENGANTAR',
                //     'ordivo' => 'ORDIVO',
                //     'parama' => 'PARAMA',
                //     'plugo' => 'PLUGO',
                //     'returnkey' => 'RETURNKEY',
                //     'sepasangcollection' => 'SEPASANGCOLLECTION',
                //     'shipperid' => 'SHIPPERID',
                //     'sirclostore' => 'SIRCLOSTORE',
                //     'tries' => 'TRIES',
                //     'grand_total' => 'Grand Total',
                //     'klien_pengirim_vip' => 'Total VIP Sumber waybill',
                // ];
                // $data['dpf_rekap_klien_vip'] = json_decode($get_periode->jsonData->dpf_pivot_vip);
                // array_unshift($data['dpf_rekap_klien_vip'], $header_dpf_rekap_klien_vip );

                $header_dpf_rekap_denda = (object) [
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
                $data['dpf_rekap_denda'] = json_decode($get_periode->jsonData->dpf_cashback_rekap_denda);
                array_unshift($data['dpf_rekap_denda'], $header_dpf_rekap_denda);

                $this->exportFileGradeDPF($data, $get_periode->month, $get_periode->year);

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

    public function exportFileGradeDPF($data ,$month, $year) {
        $file_name = strtoupper($month).'-'.$year.'-GRADING-DPF.xlsx';

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
