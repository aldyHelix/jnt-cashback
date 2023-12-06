<?
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GeneratePivotZonasiService {
    public function runZonasiGenerator($schema){
        $this->createPivotSumLuarZona($schema);
        $this->createPivotCountMPLuarZona($schema);
        $this->createPivotCountReturMPLuarZona($schema);
        $this->rekapCBLuarZonasi($schema);

        return true;
    }

    public function createPivotSumLuarZona($schema){
        $luar_zona_list = [
            'AKULAKUOB',
            'ARVEOLI',
            'BLIBLIAPI',
            'BUKAEXPRESS',
            'BUKALAPAK',
            'BUKASEND',
            'LAZADA',
            'LAZADA COD',
            'MAGELLAN',
            'MAGELLAN COD',
            'ORDIVO',
            'REGULER',
            'SHOPEE',
            'SHOPEE COD',
            'TOKOPEDIA',
            'TRIES',
            'CLODEOHQ',
            'KLIEN PENGIRIM VIP',
            'MARKETPLACE REGULER'
        ];

        $subquery = [];
        $luar_zona = [];

        foreach($luar_zona_list as $item) {
            $column = ($item != '' ? str_replace(' ','_',$item) : "");
            $as_column = ($item != "" ? str_replace(" ","_",$item) : 'blank');
            $as_column = strtolower(str_replace("-","_",$as_column));
            $subquery[] = "sum( CASE WHEN dm.sumber_waybill = '$item'::text THEN dm.biaya_kirim ELSE 0 END) AS $as_column";
            $luar_zona[] = 'sq.'.$as_column;
        };

        $subquery = implode(",\n", $subquery);
        $luar_zona_select = implode(",\n", $luar_zona);
        $luar_zona_sum = implode("+\n", $luar_zona);

        $query = "
            CREATE OR REPLACE VIEW luar_zona_sum_sumber_waybill AS
                SELECT sq.drop_point_outgoing,
                    $luar_zona_select,
                    $luar_zona_sum AS grand_total
                FROM ( SELECT dm.drop_point_outgoing,
                        $subquery
                    FROM $schema.data_mart dm
                    WHERE dm.kat = 'LUAR ZONASI'::text
                    GROUP BY dm.drop_point_outgoing) sq;
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function createPivotCountMPLuarZona($schema){

        $luar_zona_list = [
            'AKULAKUOB',
            'ARVEOLI',
            'BLIBLIAPI',
            'BUKAEXPRESS',
            'BUKALAPAK',
            'BUKASEND',
            'LAZADA',
            'LAZADA COD',
            'MAGELLAN',
            'MAGELLAN COD',
            'ORDIVO',
            'REGULER',
            'SHOPEE',
            'SHOPEE COD',
            'TOKOPEDIA',
            'TRIES',
            'CLODEOHQ',
            'KLIEN PENGIRIM VIP',
            'MARKETPLACE REGULER'
        ];

        $subquery = [];
        $luar_zona = [];

        foreach($luar_zona_list as $item) {
            $column = ($item != '' ? str_replace(' ','_',$item) : "");
            $as_column = ($item != "" ? str_replace(" ","_",$item) : 'blank');
            $as_column = strtolower(str_replace("-","_",$as_column));
            $subquery[] = "count( CASE WHEN dm.sumber_waybill = '$item'::text THEN dm.no_waybill ELSE NULL::character varying END) AS $as_column";
            $luar_zona[] = "'".$item."'::text";
        };

        $subquery = implode(",\n", $subquery);
        $luar_zona = implode(",\n ", $luar_zona);

        $query = "
            CREATE OR REPLACE VIEW luar_zona_count_waybill AS
                SELECT dm.drop_point_outgoing,
                    $subquery,
                    sum(
                        CASE
                            WHEN dm.sumber_waybill = ANY (ARRAY[$luar_zona]) THEN 1
                            ELSE 0
                        END) AS total_waybill_luar_zona
                FROM $schema.data_mart dm
                WHERE dm.kat = 'LUAR ZONASI'
                GROUP BY dm.drop_point_outgoing;
        ";

        $this->checkAndRunSchema($schema, $query);

    }

    public function createPivotCountReturMPLuarZona($schema){

        $luar_zona_list = [
            'AKULAKUOB',
            'ARVEOLI',
            'BLIBLIAPI',
            'BUKAEXPRESS',
            'BUKALAPAK',
            'BUKASEND',
            'LAZADA',
            'LAZADA COD',
            'MAGELLAN',
            'MAGELLAN COD',
            'ORDIVO',
            'REGULER',
            'SHOPEE',
            'SHOPEE COD',
            'TOKOPEDIA',
            'TRIES',
            'CLODEOHQ',
            'KLIEN PENGIRIM VIP',
            'MARKETPLACE REGULER'
        ];

        $subquery = [];
        $luar_zona = [];

        foreach($luar_zona_list as $item) {
            $column = ($item != '' ? str_replace(' ','_',$item) : "");
            $as_column = ($item != "" ? str_replace(" ","_",$item) : 'blank');
            $as_column = strtolower(str_replace("-","_",$as_column));
            $subquery[] = "count( CASE WHEN dm.sumber_waybill = '$item'::text THEN dm.no_waybill ELSE NULL::character varying END) AS $as_column";
            $luar_zona[] = "'".$item."'::text";
        };

        $subquery = implode(",\n", $subquery);
        $luar_zona = implode(",\n ", $luar_zona);

        $query = "
            CREATE OR REPLACE VIEW luar_zona_retur_count_waybill AS
                SELECT dm.drop_point_outgoing,
                    $subquery,
                    sum(
                        CASE
                            WHEN dm.sumber_waybill = ANY (ARRAY[$luar_zona]) THEN 1
                            ELSE 0
                        END) AS total_waybill_luar_zona
                FROM $schema.data_mart dm
                WHERE (dm.kat = 'LUAR ZONASI')
                    AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY dm.drop_point_outgoing;
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function rekapCBLuarZonasi($schema){
        $awb_all_bukalapak = "(lzcw.bukaexpress + lzcw.bukalapak + lzcw.bukasend)";
        $awb_all_shopee = "(lzcw.shopee + lzcw.shopee_cod)";
        $awb_all_magellan = "(lzcw.magellan + lzcw.magellan_cod)";
        $awb_all_lazada = "(lzcw.lazada + lzcw.lazada_cod)";
        $awb_retur_bukalapak = "(lzrcw.bukaexpress + lzrcw.bukalapak + lzrcw.bukasend)";
        $awb_retur_shopee = "(lzrcw.shopee + lzrcw.shopee_cod)";
        $awb_retur_magellan = "(lzrcw.magellan + lzrcw.magellan_cod)";
        $awb_retur_lazada = "(lzrcw.lazada + lzrcw.lazada_cod)";
        $awb_retur_lain_lain = "(lzrcw.akulakuob + lzrcw.tokopedia + lzrcw.ordivo + lzrcw.klien_pengirim_vip)";
        $total_awb = "(
                        (
                            $awb_all_bukalapak +
                            $awb_all_shopee +
                            $awb_all_lazada +
                            $awb_all_magellan +
                            (lzcw.tokopedia) + (lzcw.akulakuob) + (lzcw.ordivo) + lzcw.klien_pengirim_vip ) -
                        (
                            $awb_retur_shopee +
                            $awb_retur_magellan +
                            $awb_retur_lazada +
                            $awb_retur_lain_lain +
                            $awb_retur_bukalapak
                        )
                    )";
        $awb_per_waybill = 750;
        $ppn_percent = 1.1 / 100;
        $ppn = 1.011;
        $query = "
            CREATE OR REPLACE VIEW luar_zona_rekap_cashback AS
                select
                mcp.kode_cp ,
                lzcw.drop_point_outgoing ,
                $awb_all_bukalapak as awb_all_bukalapak,
                $awb_all_shopee as awb_all_shopee,
                $awb_all_lazada as awb_all_lazada,
                $awb_all_magellan as awb_all_magellan,
                (lzcw.tokopedia) as awb_tokopedia,
                (lzcw.akulakuob) as awb_akulakuob,
                (lzcw.ordivo) as awb_ordivo,
                (lzcw.klien_pengirim_vip) as awb_klien_pengirim_vip,
                $awb_retur_shopee as awb_retur_all_shopee,
                $awb_retur_magellan as awb_retur_all_magellan,
                ($awb_retur_lazada + $awb_retur_lain_lain + $awb_retur_bukalapak) as awb_retur_lain_lain,
                $total_awb as total_awb,
                ( $total_awb * $awb_per_waybill ) as diskon_awb,
                ROUND(( $total_awb * $awb_per_waybill ) * ($ppn_percent)) as cashback_ppn,
                ROUND(( $total_awb * $awb_per_waybill ) / ($ppn)) as total_cashback_luar_zonasi
                FROM $schema.luar_zona_count_waybill lzcw
                left join master_collection_point mcp on lzcw.drop_point_outgoing = mcp.drop_point_outgoing
                left join $schema.luar_zona_retur_count_waybill lzrcw on lzrcw.drop_point_outgoing = lzcw.drop_point_outgoing
                order by lzcw.drop_point_outgoing asc;
            ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function checkAndRunSchema($schema, $query){
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
