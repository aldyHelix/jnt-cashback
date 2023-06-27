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
            CREATE VIEW all_count_sum_cp_dp AS
                SELECT DISTINCT drop_point_outgoing, COUNT(no_waybill), SUM(biaya_kirim)
                FROM cashback_".$month."_".$year.".data_mart
                GROUP BY drop_point_outgoing

            CREATE VIEW reguler_count_sum_cp_dp AS
                SELECT DISTINCT data_mart.drop_point_outgoing,
                    count(data_mart.no_waybill) AS count,
                    sum(data_mart.biaya_kirim) AS sum
                    FROM cashback_".$month."_".$year.".data_mart
                WHERE (data_mart.metode_pembayaran = 'PP_PM' OR data_mart.metode_pembayaran = 'PP_CASH')
                GROUP BY data_mart.drop_point_outgoing

            CREATE VIEW dfod_count_sum_cp_dp AS
                SELECT DISTINCT data_mart.drop_point_outgoing,
                    count(data_mart.no_waybill) AS count,
                    sum(data_mart.biaya_kirim) AS SUM
                FROM cashback_".$month."_".$year.".data_mart
                WHERE (data_mart.metode_pembayaran ='CC_CASH')
                        AND (data_mart.klien_pengiriman IN ('ALWAHHIJAB', 'BLIBLIAPI', 'MAULAGI', 'TRIES', 'WEEKENDBGR', 'BITESHIP', NULL))
                GROUP BY data_mart.drop_point_outgoing

            CREATE VIEW super_count_sum_cp_dp AS
                SELECT DISTINCT data_mart.drop_point_outgoing,
                    count(data_mart.no_waybill) AS count,
                    sum(data_mart.biaya_kirim) AS SUM
                FROM cashback_".$month."_".$year.".data_mart
                WHERE data_mart.metode_pembayaran ='CC_CASH'
                        AND data_mart.klien_pengiriman IN ('SUPERINJND', 'SUPEROUT')
                GROUP BY data_mart.drop_point_outgoing

            CREATE VIEW mp_count_waybill_cp_dp AS
                SELECT dm.drop_point_outgoing,
                    COUNT(CASE WHEN dm.sumber_waybill = 'AKULAKUOB' THEN dm.no_waybill END) AS AKULAKUOB,
                    COUNT(CASE WHEN dm.sumber_waybill = 'BUKAEXPRESS' THEN dm.no_waybill END) AS BUKAEXPRESS,
                    COUNT(CASE WHEN dm.sumber_waybill = 'BUKALAPAK' THEN dm.no_waybill END) AS BUKALAPAK,
                    COUNT(CASE WHEN dm.sumber_waybill = 'BUKASEND' THEN dm.no_waybill END) AS BUKASEND,
                    COUNT(CASE WHEN dm.sumber_waybill = 'EVERMOSAPI' THEN dm.no_waybill END) AS EVERMOSAPI,
                    COUNT(CASE WHEN dm.sumber_waybill = 'LAZADA' THEN dm.no_waybill END) AS LAZADA,
                    COUNT(CASE WHEN dm.sumber_waybill = 'LAZADA COD' THEN dm.no_waybill END) AS LAZADA_COD,
                    COUNT(CASE WHEN dm.sumber_waybill = 'MAGELLAN' THEN dm.no_waybill END) AS MAGELLAN,
                    COUNT(CASE WHEN dm.sumber_waybill = 'MAGELLAN COD' THEN dm.no_waybill END) AS MAGELLAN_COD,
                    COUNT(CASE WHEN dm.sumber_waybill = 'MENGANTAR' THEN dm.no_waybill END) AS MENGANTAR,
                    COUNT(CASE WHEN dm.sumber_waybill = 'ORDIVO' THEN dm.no_waybill END) AS ORDIVO,
                    COUNT(CASE WHEN dm.sumber_waybill = 'SHOPEE' THEN dm.no_waybill END) AS SHOPEE,
                    COUNT(CASE WHEN dm.sumber_waybill = 'SHOPEE COD' THEN dm.no_waybill END) AS SHOPEE_COD,
                    COUNT(CASE WHEN dm.sumber_waybill = 'TOKOPEDIA' THEN dm.no_waybill END) AS TOKOPEDIA,
                    SUM(CASE WHEN dm.sumber_waybill IN ('AKULAKUOB', 'BUKAEXPRESS', 'BUKALAPAK', 'BUKASEND', 'EVERMOSAPI', 'LAZADA', 'LAZADA COD', 'MAGELLAN', 'MAGELLAN COD', 'MENGANTAR', 'ORDIVO', 'SHOPEE', 'SHOPEE COD', 'TOKOPEDIA') THEN 1 ELSE 0 END) AS grand_total
                FROM
                    cashback_".$month."_".$year.".data_mart dm
                GROUP BY
                    dm.drop_point_outgoing

                CREATE VIEW mp_sum_biaya_kirim AS
                    SELECT
                        drop_point_outgoing,
                        AKULAKUOB,
                        BUKAEXPRESS,
                        BUKALAPAK,
                        BUKASEND,
                        EVERMOSAPI,
                        LAZADA,
                        LAZADA_COD,
                        MAGELLAN,
                        MAGELLAN_COD,
                        MENGANTAR,
                        ORDIVO,
                        SHOPEE,
                        SHOPEE_COD,
                        TOKOPEDIA,
                        (AKULAKUOB + BUKAEXPRESS + BUKALAPAK + BUKASEND + EVERMOSAPI + LAZADA + LAZADA_COD + MAGELLAN + MAGELLAN_COD + MENGANTAR + ORDIVO + SHOPEE + SHOPEE_COD + TOKOPEDIA) AS grand_total
                    FROM (
                        SELECT
                            dm.drop_point_outgoing,
                            SUM(CASE WHEN dm.sumber_waybill = 'AKULAKUOB' THEN dm.biaya_kirim ELSE 0 END) AS AKULAKUOB,
                            SUM(CASE WHEN dm.sumber_waybill = 'BUKAEXPRESS' THEN dm.biaya_kirim ELSE 0 END) AS BUKAEXPRESS,
                            SUM(CASE WHEN dm.sumber_waybill = 'BUKALAPAK' THEN dm.biaya_kirim ELSE 0 END) AS BUKALAPAK,
                            SUM(CASE WHEN dm.sumber_waybill = 'BUKASEND' THEN dm.biaya_kirim ELSE 0 END) AS BUKASEND,
                            SUM(CASE WHEN dm.sumber_waybill = 'EVERMOSAPI' THEN dm.biaya_kirim ELSE 0 END) AS EVERMOSAPI,
                            SUM(CASE WHEN dm.sumber_waybill = 'LAZADA' THEN dm.biaya_kirim ELSE 0 END) AS LAZADA,
                            SUM(CASE WHEN dm.sumber_waybill = 'LAZADA COD' THEN dm.biaya_kirim ELSE 0 END) AS LAZADA_COD,
                            SUM(CASE WHEN dm.sumber_waybill = 'MAGELLAN' THEN dm.biaya_kirim ELSE 0 END) AS MAGELLAN,
                            SUM(CASE WHEN dm.sumber_waybill = 'MAGELLAN COD' THEN dm.biaya_kirim ELSE 0 END) AS MAGELLAN_COD,
                            SUM(CASE WHEN dm.sumber_waybill = 'MENGANTAR' THEN dm.biaya_kirim ELSE 0 END) AS MENGANTAR,
                            SUM(CASE WHEN dm.sumber_waybill = 'ORDIVO' THEN dm.biaya_kirim ELSE 0 END) AS ORDIVO,
                            SUM(CASE WHEN dm.sumber_waybill = 'SHOPEE' THEN dm.biaya_kirim ELSE 0 END) AS SHOPEE,
                            SUM(CASE WHEN dm.sumber_waybill = 'SHOPEE COD' THEN dm.biaya_kirim ELSE 0 END) AS SHOPEE_COD,
                            SUM(CASE WHEN dm.sumber_waybill = 'TOKOPEDIA' THEN dm.biaya_kirim ELSE 0 END) AS TOKOPEDIA
                        FROM
                            cashback_".$month."_".$year.".data_mart dm
                        GROUP BY
                            dm.drop_point_outgoing
                    ) AS subquery

                CREATE VIEW mp_count_no_waybill AS
                    SELECT dm.drop_point_outgoing,
                    COUNT(CASE WHEN dm.sumber_waybill = 'AKULAKUOB' THEN dm.no_waybill END) AS AKULAKUOB,
                    COUNT(CASE WHEN dm.sumber_waybill = 'BUKAEXPRESS' THEN dm.no_waybill END) AS BUKAEXPRESS,
                    COUNT(CASE WHEN dm.sumber_waybill = 'BUKALAPAK' THEN dm.no_waybill END) AS BUKALAPAK,
                    COUNT(CASE WHEN dm.sumber_waybill = 'BUKASEND' THEN dm.no_waybill END) AS BUKASEND,
                    COUNT(CASE WHEN dm.sumber_waybill = 'EVERMOSAPI' THEN dm.no_waybill END) AS EVERMOSAPI,
                    COUNT(CASE WHEN dm.sumber_waybill = 'LAZADA' THEN dm.no_waybill END) AS LAZADA,
                    COUNT(CASE WHEN dm.sumber_waybill = 'LAZADA COD' THEN dm.no_waybill END) AS LAZADA_COD,
                    COUNT(CASE WHEN dm.sumber_waybill = 'MAGELLAN' THEN dm.no_waybill END) AS MAGELLAN,
                    COUNT(CASE WHEN dm.sumber_waybill = 'MAGELLAN COD' THEN dm.no_waybill END) AS MAGELLAN_COD,
                    COUNT(CASE WHEN dm.sumber_waybill = 'MENGANTAR' THEN dm.no_waybill END) AS MENGANTAR,
                    COUNT(CASE WHEN dm.sumber_waybill = 'ORDIVO' THEN dm.no_waybill END) AS ORDIVO,
                    COUNT(CASE WHEN dm.sumber_waybill = 'SHOPEE' THEN dm.no_waybill END) AS SHOPEE,
                    COUNT(CASE WHEN dm.sumber_waybill = 'SHOPEE COD' THEN dm.no_waybill END) AS SHOPEE_COD,
                    COUNT(CASE WHEN dm.sumber_waybill = 'TOKOPEDIA' THEN dm.no_waybill END) AS TOKOPEDIA,
                    SUM(CASE WHEN dm.sumber_waybill IN ('AKULAKUOB', 'BUKAEXPRESS', 'BUKALAPAK', 'BUKASEND', 'EVERMOSAPI', 'LAZADA', 'LAZADA COD', 'MAGELLAN', 'MAGELLAN COD', 'MENGANTAR', 'ORDIVO', 'SHOPEE', 'SHOPEE COD', 'TOKOPEDIA') THEN 1 ELSE 0 END) AS grand_total
                FROM
                    cashback_".$month."_".$year.".data_mart dm
                WHERE
                    dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1)
                GROUP BY
                    dm.drop_point_outgoing

                CREATE VIEW mp_retur_sum_biaya_kirim AS
                    SELECT
                        drop_point_outgoing,
                        AKULAKUOB,
                        BUKAEXPRESS,
                        BUKALAPAK,
                        BUKASEND,
                        EVERMOSAPI,
                        LAZADA,
                        LAZADA_COD,
                        MAGELLAN,
                        MAGELLAN_COD,
                        MENGANTAR,
                        ORDIVO,
                        SHOPEE,
                        SHOPEE_COD,
                        TOKOPEDIA,
                        (AKULAKUOB + BUKAEXPRESS + BUKALAPAK + BUKASEND + EVERMOSAPI + LAZADA + LAZADA_COD + MAGELLAN + MAGELLAN_COD + MENGANTAR + ORDIVO + SHOPEE + SHOPEE_COD + TOKOPEDIA) AS grand_total
                    FROM (
                        SELECT
                            dm.drop_point_outgoing,
                            SUM(CASE WHEN dm.sumber_waybill = 'AKULAKUOB' THEN dm.biaya_kirim ELSE 0 END) AS AKULAKUOB,
                            SUM(CASE WHEN dm.sumber_waybill = 'BUKAEXPRESS' THEN dm.biaya_kirim ELSE 0 END) AS BUKAEXPRESS,
                            SUM(CASE WHEN dm.sumber_waybill = 'BUKALAPAK' THEN dm.biaya_kirim ELSE 0 END) AS BUKALAPAK,
                            SUM(CASE WHEN dm.sumber_waybill = 'BUKASEND' THEN dm.biaya_kirim ELSE 0 END) AS BUKASEND,
                            SUM(CASE WHEN dm.sumber_waybill = 'EVERMOSAPI' THEN dm.biaya_kirim ELSE 0 END) AS EVERMOSAPI,
                            SUM(CASE WHEN dm.sumber_waybill = 'LAZADA' THEN dm.biaya_kirim ELSE 0 END) AS LAZADA,
                            SUM(CASE WHEN dm.sumber_waybill = 'LAZADA COD' THEN dm.biaya_kirim ELSE 0 END) AS LAZADA_COD,
                            SUM(CASE WHEN dm.sumber_waybill = 'MAGELLAN' THEN dm.biaya_kirim ELSE 0 END) AS MAGELLAN,
                            SUM(CASE WHEN dm.sumber_waybill = 'MAGELLAN COD' THEN dm.biaya_kirim ELSE 0 END) AS MAGELLAN_COD,
                            SUM(CASE WHEN dm.sumber_waybill = 'MENGANTAR' THEN dm.biaya_kirim ELSE 0 END) AS MENGANTAR,
                            SUM(CASE WHEN dm.sumber_waybill = 'ORDIVO' THEN dm.biaya_kirim ELSE 0 END) AS ORDIVO,
                            SUM(CASE WHEN dm.sumber_waybill = 'SHOPEE' THEN dm.biaya_kirim ELSE 0 END) AS SHOPEE,
                            SUM(CASE WHEN dm.sumber_waybill = 'SHOPEE COD' THEN dm.biaya_kirim ELSE 0 END) AS SHOPEE_COD,
                            SUM(CASE WHEN dm.sumber_waybill = 'TOKOPEDIA' THEN dm.biaya_kirim ELSE 0 END) AS TOKOPEDIA
                        FROM
                            cashback_".$month."_".$year.".data_mart dm
                        WHERE
                            dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1)
                        GROUP BY
                            dm.drop_point_outgoing
                    ) AS subquery
            ");
            // CREATE VIEW winners AS
            //     SELECT title, release FROM films WHERE awards IS NOT NULL;
        return $created;
	}

    public function createViewAllCPDP() {
        $query = `
        CREATE VIEW all_count_sum_cp_dp AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS sum
                FROM data_mart
            GROUP BY data_mart.drop_point_outgoing;`;
    }

    public function createViewRegulerCPDP() {
        $query = `
        CREATE VIEW reguler_count_sum_cp_dp AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS sum
                FROM data_mart
            WHERE (data_mart.metode_pembayaran = 'PP_PM' OR data_mart.metode_pembayaran = 'PP_CASH')
            GROUP BY data_mart.drop_point_outgoing;`;
    }

    public function createViewDFODCPDP() {
        $query = `
        CREATE VIEW dfod_count_sum_cp_dp AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM data_mart
            WHERE (data_mart.metode_pembayaran ='CC_CASH')
                    AND (data_mart.klien_pengiriman IN ('ALWAHHIJAB', 'BLIBLIAPI', 'MAULAGI', 'TRIES', 'WEEKENDBGR', 'BITESHIP', NULL))
            GROUP BY data_mart.drop_point_outgoing;
        `;
    }

    public function createViewSuperCPDP() {
        $query = `
        CREATE VIEW super_count_sum_cp_dp AS
        SELECT DISTINCT data_mart.drop_point_outgoing,
            count(data_mart.no_waybill) AS count,
            sum(data_mart.biaya_kirim) AS SUM
        FROM data_mart
        WHERE data_mart.metode_pembayaran ='CC_CASH'
                AND data_mart.klien_pengiriman IN ('SUPERINJND', 'SUPEROUT')
        GROUP BY data_mart.drop_point_outgoing;
        `;
    }

    public function createViewMPCountWaybillCPDP() {
        $query = `CREATE VIEW mp_count_waybill_cp_dp AS
        SELECT dm.drop_point_outgoing,
            COUNT(CASE WHEN dm.sumber_waybill = 'AKULAKUOB' THEN dm.no_waybill END) AS AKULAKUOB,
            COUNT(CASE WHEN dm.sumber_waybill = 'BUKAEXPRESS' THEN dm.no_waybill END) AS BUKAEXPRESS,
            COUNT(CASE WHEN dm.sumber_waybill = 'BUKALAPAK' THEN dm.no_waybill END) AS BUKALAPAK,
            COUNT(CASE WHEN dm.sumber_waybill = 'BUKASEND' THEN dm.no_waybill END) AS BUKASEND,
            COUNT(CASE WHEN dm.sumber_waybill = 'EVERMOSAPI' THEN dm.no_waybill END) AS EVERMOSAPI,
            COUNT(CASE WHEN dm.sumber_waybill = 'LAZADA' THEN dm.no_waybill END) AS LAZADA,
            COUNT(CASE WHEN dm.sumber_waybill = 'LAZADA COD' THEN dm.no_waybill END) AS LAZADA_COD,
            COUNT(CASE WHEN dm.sumber_waybill = 'MAGELLAN' THEN dm.no_waybill END) AS MAGELLAN,
            COUNT(CASE WHEN dm.sumber_waybill = 'MAGELLAN COD' THEN dm.no_waybill END) AS MAGELLAN_COD,
            COUNT(CASE WHEN dm.sumber_waybill = 'MENGANTAR' THEN dm.no_waybill END) AS MENGANTAR,
            COUNT(CASE WHEN dm.sumber_waybill = 'ORDIVO' THEN dm.no_waybill END) AS ORDIVO,
            COUNT(CASE WHEN dm.sumber_waybill = 'SHOPEE' THEN dm.no_waybill END) AS SHOPEE,
            COUNT(CASE WHEN dm.sumber_waybill = 'SHOPEE COD' THEN dm.no_waybill END) AS SHOPEE_COD,
            COUNT(CASE WHEN dm.sumber_waybill = 'TOKOPEDIA' THEN dm.no_waybill END) AS TOKOPEDIA,
            SUM(CASE WHEN dm.sumber_waybill IN ('AKULAKUOB', 'BUKAEXPRESS', 'BUKALAPAK', 'BUKASEND', 'EVERMOSAPI', 'LAZADA', 'LAZADA COD', 'MAGELLAN', 'MAGELLAN COD', 'MENGANTAR', 'ORDIVO', 'SHOPEE', 'SHOPEE COD', 'TOKOPEDIA') THEN 1 ELSE 0 END) AS grand_total
            FROM
            data_mart dm
            GROUP BY
            dm.drop_point_outgoing;
        `;
    }

    public function createViewMPSumBiayaKirimCPDP() {
        $query = `
            CREATE VIEW mp_sum_biaya_kirim AS
            SELECT
                drop_point_outgoing,
                AKULAKUOB,
                BUKAEXPRESS,
                BUKALAPAK,
                BUKASEND,
                EVERMOSAPI,
                LAZADA,
                LAZADA_COD,
                MAGELLAN,
                MAGELLAN_COD,
                MENGANTAR,
                ORDIVO,
                SHOPEE,
                SHOPEE_COD,
                TOKOPEDIA,
                (AKULAKUOB + BUKAEXPRESS + BUKALAPAK + BUKASEND + EVERMOSAPI + LAZADA + LAZADA_COD + MAGELLAN + MAGELLAN_COD + MENGANTAR + ORDIVO + SHOPEE + SHOPEE_COD + TOKOPEDIA) AS grand_total
            FROM (
                SELECT
                dm.drop_point_outgoing,
                SUM(CASE WHEN dm.sumber_waybill = 'AKULAKUOB' THEN dm.biaya_kirim ELSE 0 END) AS AKULAKUOB,
                SUM(CASE WHEN dm.sumber_waybill = 'BUKAEXPRESS' THEN dm.biaya_kirim ELSE 0 END) AS BUKAEXPRESS,
                SUM(CASE WHEN dm.sumber_waybill = 'BUKALAPAK' THEN dm.biaya_kirim ELSE 0 END) AS BUKALAPAK,
                SUM(CASE WHEN dm.sumber_waybill = 'BUKASEND' THEN dm.biaya_kirim ELSE 0 END) AS BUKASEND,
                SUM(CASE WHEN dm.sumber_waybill = 'EVERMOSAPI' THEN dm.biaya_kirim ELSE 0 END) AS EVERMOSAPI,
                SUM(CASE WHEN dm.sumber_waybill = 'LAZADA' THEN dm.biaya_kirim ELSE 0 END) AS LAZADA,
                SUM(CASE WHEN dm.sumber_waybill = 'LAZADA COD' THEN dm.biaya_kirim ELSE 0 END) AS LAZADA_COD,
                SUM(CASE WHEN dm.sumber_waybill = 'MAGELLAN' THEN dm.biaya_kirim ELSE 0 END) AS MAGELLAN,
                SUM(CASE WHEN dm.sumber_waybill = 'MAGELLAN COD' THEN dm.biaya_kirim ELSE 0 END) AS MAGELLAN_COD,
                SUM(CASE WHEN dm.sumber_waybill = 'MENGANTAR' THEN dm.biaya_kirim ELSE 0 END) AS MENGANTAR,
                SUM(CASE WHEN dm.sumber_waybill = 'ORDIVO' THEN dm.biaya_kirim ELSE 0 END) AS ORDIVO,
                SUM(CASE WHEN dm.sumber_waybill = 'SHOPEE' THEN dm.biaya_kirim ELSE 0 END) AS SHOPEE,
                SUM(CASE WHEN dm.sumber_waybill = 'SHOPEE COD' THEN dm.biaya_kirim ELSE 0 END) AS SHOPEE_COD,
                SUM(CASE WHEN dm.sumber_waybill = 'TOKOPEDIA' THEN dm.biaya_kirim ELSE 0 END) AS TOKOPEDIA
                FROM
                data_mart dm
                GROUP BY
                dm.drop_point_outgoing
            ) AS subquery
        `;
    }

    public function createViewMPReturCountWaybillCPDP() {
        $query = `
            CREATE VIEW mp_sum_biaya_kirim AS
                SELECT dm.drop_point_outgoing,
                COUNT(CASE WHEN dm.sumber_waybill = 'AKULAKUOB' THEN dm.no_waybill END) AS AKULAKUOB,
                COUNT(CASE WHEN dm.sumber_waybill = 'BUKAEXPRESS' THEN dm.no_waybill END) AS BUKAEXPRESS,
                COUNT(CASE WHEN dm.sumber_waybill = 'BUKALAPAK' THEN dm.no_waybill END) AS BUKALAPAK,
                COUNT(CASE WHEN dm.sumber_waybill = 'BUKASEND' THEN dm.no_waybill END) AS BUKASEND,
                COUNT(CASE WHEN dm.sumber_waybill = 'EVERMOSAPI' THEN dm.no_waybill END) AS EVERMOSAPI,
                COUNT(CASE WHEN dm.sumber_waybill = 'LAZADA' THEN dm.no_waybill END) AS LAZADA,
                COUNT(CASE WHEN dm.sumber_waybill = 'LAZADA COD' THEN dm.no_waybill END) AS LAZADA_COD,
                COUNT(CASE WHEN dm.sumber_waybill = 'MAGELLAN' THEN dm.no_waybill END) AS MAGELLAN,
                COUNT(CASE WHEN dm.sumber_waybill = 'MAGELLAN COD' THEN dm.no_waybill END) AS MAGELLAN_COD,
                COUNT(CASE WHEN dm.sumber_waybill = 'MENGANTAR' THEN dm.no_waybill END) AS MENGANTAR,
                COUNT(CASE WHEN dm.sumber_waybill = 'ORDIVO' THEN dm.no_waybill END) AS ORDIVO,
                COUNT(CASE WHEN dm.sumber_waybill = 'SHOPEE' THEN dm.no_waybill END) AS SHOPEE,
                COUNT(CASE WHEN dm.sumber_waybill = 'SHOPEE COD' THEN dm.no_waybill END) AS SHOPEE_COD,
                COUNT(CASE WHEN dm.sumber_waybill = 'TOKOPEDIA' THEN dm.no_waybill END) AS TOKOPEDIA,
                SUM(CASE WHEN dm.sumber_waybill IN ('AKULAKUOB', 'BUKAEXPRESS', 'BUKALAPAK', 'BUKASEND', 'EVERMOSAPI', 'LAZADA', 'LAZADA COD', 'MAGELLAN', 'MAGELLAN COD', 'MENGANTAR', 'ORDIVO', 'SHOPEE', 'SHOPEE COD', 'TOKOPEDIA') THEN 1 ELSE 0 END) AS grand_total
            FROM
                data_mart dm
            WHERE
                dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1)
            GROUP BY
                dm.drop_point_outgoing;
        `;
    }

    public function createViewMPReturSumBiayaKirimCPDP() {
        $query = `
            CREATE VIEW mp_sum_biaya_kirim AS
            SELECT
                drop_point_outgoing,
                AKULAKUOB,
                BUKAEXPRESS,
                BUKALAPAK,
                BUKASEND,
                EVERMOSAPI,
                LAZADA,
                LAZADA_COD,
                MAGELLAN,
                MAGELLAN_COD,
                MENGANTAR,
                ORDIVO,
                SHOPEE,
                SHOPEE_COD,
                TOKOPEDIA,
                (AKULAKUOB + BUKAEXPRESS + BUKALAPAK + BUKASEND + EVERMOSAPI + LAZADA + LAZADA_COD + MAGELLAN + MAGELLAN_COD + MENGANTAR + ORDIVO + SHOPEE + SHOPEE_COD + TOKOPEDIA) AS grand_total
            FROM (
                SELECT
                    dm.drop_point_outgoing,
                    SUM(CASE WHEN dm.sumber_waybill = 'AKULAKUOB' THEN dm.biaya_kirim ELSE 0 END) AS AKULAKUOB,
                    SUM(CASE WHEN dm.sumber_waybill = 'BUKAEXPRESS' THEN dm.biaya_kirim ELSE 0 END) AS BUKAEXPRESS,
                    SUM(CASE WHEN dm.sumber_waybill = 'BUKALAPAK' THEN dm.biaya_kirim ELSE 0 END) AS BUKALAPAK,
                    SUM(CASE WHEN dm.sumber_waybill = 'BUKASEND' THEN dm.biaya_kirim ELSE 0 END) AS BUKASEND,
                    SUM(CASE WHEN dm.sumber_waybill = 'EVERMOSAPI' THEN dm.biaya_kirim ELSE 0 END) AS EVERMOSAPI,
                    SUM(CASE WHEN dm.sumber_waybill = 'LAZADA' THEN dm.biaya_kirim ELSE 0 END) AS LAZADA,
                    SUM(CASE WHEN dm.sumber_waybill = 'LAZADA COD' THEN dm.biaya_kirim ELSE 0 END) AS LAZADA_COD,
                    SUM(CASE WHEN dm.sumber_waybill = 'MAGELLAN' THEN dm.biaya_kirim ELSE 0 END) AS MAGELLAN,
                    SUM(CASE WHEN dm.sumber_waybill = 'MAGELLAN COD' THEN dm.biaya_kirim ELSE 0 END) AS MAGELLAN_COD,
                    SUM(CASE WHEN dm.sumber_waybill = 'MENGANTAR' THEN dm.biaya_kirim ELSE 0 END) AS MENGANTAR,
                    SUM(CASE WHEN dm.sumber_waybill = 'ORDIVO' THEN dm.biaya_kirim ELSE 0 END) AS ORDIVO,
                    SUM(CASE WHEN dm.sumber_waybill = 'SHOPEE' THEN dm.biaya_kirim ELSE 0 END) AS SHOPEE,
                    SUM(CASE WHEN dm.sumber_waybill = 'SHOPEE COD' THEN dm.biaya_kirim ELSE 0 END) AS SHOPEE_COD,
                    SUM(CASE WHEN dm.sumber_waybill = 'TOKOPEDIA' THEN dm.biaya_kirim ELSE 0 END) AS TOKOPEDIA
                FROM
                    data_mart dm
                WHERE
                    dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1)
                GROUP BY
                    dm.drop_point_outgoing
            ) AS subquery
        `;
    }
}
