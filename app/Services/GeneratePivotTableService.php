<?
namespace App\Services;

use App\Models\PeriodeKlienPengiriman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Category\Models\CategoryKlienPengiriman;

class GeneratePivotTableService {
    public function createPivot($schema, $periode_id) {
        //get category
        $category = CategoryKlienPengiriman::get();
        $query = "";
        // $schema = 'cashback_feb_2022'; //for debuging

        $query .= "
            CREATE OR REPLACE VIEW sum_all_biaya_kirim AS
                SELECT SUM(data_mart.biaya_kirim)
                FROM ".$schema.".data_mart;";

        foreach($category as $cat) {
            //get periode klien pengiriman
            $periode_klien_pengiriman = PeriodeKlienPengiriman::with('klien_pengiriman')->where(['periode_id' => $periode_id, 'category_id'=> $cat->id])->get()->pluck('klien_pengiriman.klien_pengiriman')->toArray();

            //get KAT
            $kat = "";
            //get metode pembayaran
            $kat = str_replace(";","' OR data_mart.kat = '",$cat->kat);
            $kat = "data_mart.kat = '".$kat."'";
            $metode_pembayaran = "";
            $metode_pembayaran = str_replace(";","' OR data_mart.metode_pembayaran = '",$cat->metode_pembayaran);
            $metode_pembayaran = str_replace("(blank)"," ",$metode_pembayaran);
            $metode_pembayaran = "data_mart.metode_pembayaran = '".$metode_pembayaran."'";

            $klien_pengiriman = "";


            $klien_pengiriman = implode(";", $periode_klien_pengiriman);
            $klien_pengiriman = str_replace(";","', '",$klien_pengiriman);
            $klien_pengiriman = "'".$klien_pengiriman."'";
            $klien_pengiriman = str_replace("''","' ',NULL ",$klien_pengiriman);

            $query .= "
                CREATE OR REPLACE VIEW cp_dp_".$cat->kode_kategori."_count_sum AS
                    SELECT DISTINCT data_mart.drop_point_outgoing,
                        count(data_mart.no_waybill) AS count,
                        sum(data_mart.biaya_kirim) AS sum
                        FROM ".$schema.".data_mart
                    WHERE
                    ($kat)
                    AND
                    ($metode_pembayaran)
                    AND
                    (data_mart.klien_pengiriman IN ( $klien_pengiriman ))
                    GROUP BY data_mart.drop_point_outgoing;

            ";
        }

        //check if exist first
        if(Schema::hasTable($schema.'.data_mart')) {
            $run = DB::connection('pgsql')->unprepared(
                "
                SET search_path TO $schema, public; \n

                ".$query."
            ");

            return $run;
        };

        return false;
    }


}
