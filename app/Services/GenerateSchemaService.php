<?
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GenerateSchemaService {
    public function createSchemaCashback($schema_name){
        //check if exist first
        if(Schema::hasTable($schema_name.'.data_mart')) {
            return false;
        }

		$created = DB::connection('pgsql')->unprepared("
            CREATE SCHEMA ".$schema_name."
            CREATE TABLE data_mart (no_waybill varchar unique, tgl_pengiriman date, drop_point_outgoing varchar, sprinter_pickup text,tempat_tujuan text,keterangan text, berat_yang_ditagih float, cod integer, biaya_asuransi integer, biaya_kirim integer, biaya_lainnya integer, total_biaya integer, klien_pengiriman text, metode_pembayaran text, nama_pengirim text, sumber_waybill text, paket_retur text, waktu_ttd timestamp, layanan text, diskon integer, total_biaya_setelah_diskon integer, agen_tujuan text, nik text, kode_promo text, kat text)");
            // CREATE VIEW winners AS
            //     SELECT title, release FROM films WHERE awards IS NOT NULL;
        return $created;
	}
}
