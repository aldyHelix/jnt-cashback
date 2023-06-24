<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSchemaService {
	public function createSchema($month, $year){
        //check if exist first
        if(Schema::hasTable('cashback_'.$month.'_'.$year.'.data_mart')) {
            return false;
        }
		$created = DB::connection('pgsql')->unprepared("
            CREATE SCHEMA cashback_".$month."_".$year."
            CREATE TABLE data_mart (no_waybill varchar unique, tgl_pengiriman date, drop_point_outgoing varchar, sprinter_pickup text,tempat_tujuan text,keterangan text, berat_yang_ditagih float, cod integer, biaya_asuransi integer, biaya_kirim integer, biaya_lainnya integer, total_biaya integer, klien_pengiriman text, metode_pembayaran text, nama_pengirim text, sumber_waybill text, paket_retur text, waktu_ttd timestamp, layanan text, diskon integer, total_biaya_setelah_diskon integer, agen_tujuan text, nik text, kode_promo text, kat text)
            CREATE VIEW reguler_count_sum_cp_dp AS
                SELECT DISTINCT drop_point_outgoing, COUNT(no_waybill), SUM(biaya_kirim)
                FROM cashback_".$month."_".$year.".data_mart
                GROUP BY drop_point_outgoing
            ");
            // CREATE VIEW winners AS
            //     SELECT title, release FROM films WHERE awards IS NOT NULL;
        return $created;
	}
}
