<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSchemaService {

    public function createSchemaDelivery($month, $year) {
        //check if exist first
        if(Schema::hasTable('delivery_'.$month.'_'.$year.'.data_mart')) {
            return false;
        }
        $schema = "delivery_".$month."_".$year;

        $created = DB::connection('pgsql')->unprepared("
            CREATE SCHEMA ".$schema."
            CREATE TABLE data_mart (
            drop_point_outgoing varchar,
            drop_point_ttd varchar,
            waktu_ttd timestamp,
            no_waybill varchar unique,
            sprinter text,
            tempat_tujuan text,
            layanan text,
            berat float
            )
            ".$this->MPDeliveryAWB($schema)."

        ");

        return $created;
    }

    public function MPDeliveryAWB($schema) {
        return "
            CREATE OR REPLACE VIEW mp_delivery_count_sprinter AS
            SELECT DISTINCT data_mart.sprinter,
            COUNT(data_mart.no_waybill),
            CASE
               WHEN COUNT(data_mart.no_waybill) < 2200 THEN 0
               WHEN COUNT(data_mart.no_waybill) BETWEEN 2200 AND 2400 THEN 50
               ELSE 100
            END AS fee_condition_value
            FROM ".$schema.".data_mart
            GROUP BY data_mart.sprinter";
    }

	public function createSchemaCashback($month, $year){
        //check if exist first
        if(Schema::hasTable('cashback_'.$month.'_'.$year.'.data_mart')) {
            return false;
        }
        $schema = "cashback_".$month."_".$year;
		$created = DB::connection('pgsql')->unprepared("
            CREATE SCHEMA ".$schema."
            CREATE TABLE data_mart (no_waybill varchar unique, tgl_pengiriman date, drop_point_outgoing varchar, sprinter_pickup text,tempat_tujuan text,keterangan text, berat_yang_ditagih float, cod integer, biaya_asuransi integer, biaya_kirim integer, biaya_lainnya integer, total_biaya integer, klien_pengiriman text, metode_pembayaran text, nama_pengirim text, sumber_waybill text, paket_retur text, waktu_ttd timestamp, layanan text, diskon integer, total_biaya_setelah_diskon integer, agen_tujuan text, nik text, kode_promo text, kat text)

            ".$this->allSumBiayaKirim($schema)."

            ".$this->CPDPAllCountSum($schema)."

            ".$this->CPDPRegulerCountSum($schema)."

            ".$this->CPDPDfodCountSum($schema)."

            ".$this->CPDPSuperCountSum($schema)."

            ".$this->CPDPMPCountWaybill($schema)."

            ".$this->CPDPMPSumBiayaKirim($schema)."

            ".$this->CPDPMPReturCountWaybill($schema)."

            ".$this->CPDPMPReturSumBiayaKirim($schema)."

            ".$this->DPFAllCountSum($schema)."

            ".$this->DPFRegulerCountSum($schema)."

            ".$this->DPFDfodCountSum($schema)."

            ".$this->DPFSuperCountSum($schema)."

            ".$this->DPFMPCountWaybill($schema)."

            ".$this->DPFMPSumBiayaKirim($schema)."

            ".$this->DPFMPReturCountWaybill($schema)."

            ".$this->DPFMPReturSumBiayaKirim($schema)."

            ".$this->ZonasiAllCountSum($schema)."

            ".$this->ZonasiRegulerCountSum($schema)."

            ".$this->ZonasiDfodCountSum($schema)."

            ".$this->ZonasiSuperCountSum($schema)."

            ".$this->ZonasiMPCountWaybill($schema)."

            ".$this->ZonasiMPSumBiayaKirim($schema)."

            ".$this->ZonasiMPReturCountWaybill($schema)."

            ".$this->ZonasiMPReturSumBiayaKirim($schema)."

            ".$this->DCAllCountSum($schema)."

            ".$this->DCRegulerCountSum($schema)."

            ".$this->DCDfodCountSum($schema)."

            ".$this->DCSuperCountSum($schema)."

            ".$this->DCMPCountWaybill($schema)."

            ".$this->DCMPSumBiayaKirim($schema)."

            ".$this->DCMPReturCountWaybill($schema)."

            ".$this->DCMPReturSumBiayaKirim($schema)."

            ".$this->DCAllCountSum($schema)."

            ".$this->DCRegulerCountSum($schema)."

            ".$this->DCDfodCountSum($schema)."

            ".$this->DCSuperCountSum($schema)."

            ".$this->DCMPCountWaybill($schema)."

            ".$this->DCMPSumBiayaKirim($schema)."

            ".$this->DCMPReturCountWaybill($schema)."

            ".$this->DCMPReturSumBiayaKirim($schema)."

            ".$this->createViewRekapZonasi($schema)."

            ".$this->createViewCPDPCashbackRegulerGrading1($schema)."

            ".$this->createViewCPDPCashbackRegulerGrading2($schema)."

            ".$this->createViewCPDPCashbackRegulerGrading3($schema)."

            ".$this->createViewCPDPCashbackCODGrading1($schema)."

            ".$this->createViewCPDPCashbackNonCODGrading1($schema)."

            ".$this->createViewCPDPCashbackRekapGrading1($schema)."

            ".$this->createViewCPDPCashbackRekapDendaGrading1($schema)."

            ".$this->createViewCPDPCashbackAWBGrading2($schema)."

            ".$this->createViewCPDPCashbackRekapGrading2($schema)."

            ".$this->createViewCPDPCashbackRekapDendaGrading2($schema)."

            ".$this->createViewCPDPCashbackAWBGrading3($schema)."

            ".$this->createViewCPDPCashbackRekapGrading3($schema)."

            ".$this->createViewCPDPCashbackRekapDendaGrading3($schema)."

            ");
            // CREATE VIEW winners AS
            //     SELECT title, release FROM films WHERE awards IS NOT NULL;
        return $created;
	}

    public function allSumBiayaKirim($schema) {
        return "
            CREATE OR REPLACE VIEW sum_all_biaya_kirim AS
            SELECT SUM(data_mart.biaya_kirim)
            FROM ".$schema.".data_mart
        ";
    }

    public function CPDPAllCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW cp_dp_all_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing, COUNT(data_mart.no_waybill), SUM(data_mart.biaya_kirim)
                FROM ".$schema.".data_mart
                WHERE (data_mart.kat = 'CP' OR data_mart.kat = 'DP')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function CPDPRegulerCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW cp_dp_reguler_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing,
                    count(data_mart.no_waybill) AS count,
                    sum(data_mart.biaya_kirim) AS sum
                    FROM ".$schema.".data_mart
                WHERE
                (data_mart.kat = 'CP' OR data_mart.kat = 'DP')
                AND
                (data_mart.metode_pembayaran = 'PP_PM' OR data_mart.metode_pembayaran = 'PP_CASH')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function CPDPDfodCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW cp_dp_dfod_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = 'CP' OR data_mart.kat = 'DP')
            AND (data_mart.metode_pembayaran ='CC_CASH')
                    AND (data_mart.klien_pengiriman IN ('ALWAHHIJAB', 'BLIBLIAPI', 'MAULAGI', 'TRIES', 'WEEKENDBGR', 'BITESHIP', NULL))
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function CPDPSuperCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW cp_dp_super_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = 'CP' OR data_mart.kat = 'DP')
            AND (data_mart.metode_pembayaran ='CC_CASH'
                    AND data_mart.klien_pengiriman IN ('SUPERINJND', 'SUPEROUT'))
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function CPDPMPCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW cp_dp_mp_count_waybill AS
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
                    ".$schema.".data_mart dm
                WHERE (dm.kat = 'CP' OR dm.kat = 'DP')
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function CPDPMPSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW cp_dp_mp_sum_biaya_kirim AS
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
                    ".$schema.".data_mart dm
                    WHERE (dm.kat = 'CP' OR dm.kat = 'DP')
                    GROUP BY
                        dm.drop_point_outgoing
                ) AS subquery";
    }

    public function CPDPMPReturCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW cp_dp_mp_retur_count_waybill AS
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
                ".$schema.".data_mart dm
                WHERE (dm.kat = 'CP' OR dm.kat = 'DP')
                AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function CPDPMPReturSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW cp_dp_mp_retur_sum_biaya_kirim AS
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
            ".$schema.".data_mart dm
            WHERE (dm.kat = 'CP' OR dm.kat = 'DP')
            AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
            GROUP BY
                dm.drop_point_outgoing
        ) AS subquery";
    }

    public function DPFAllCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW dpf_all_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing, COUNT(data_mart.no_waybill), SUM(data_mart.biaya_kirim)
                FROM ".$schema.".data_mart
                WHERE (data_mart.kat = 'DPF')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function DPFRegulerCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW dpf_reguler_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing,
                    count(data_mart.no_waybill) AS count,
                    sum(data_mart.biaya_kirim) AS sum
                    FROM ".$schema.".data_mart
                WHERE (data_mart.kat = 'DPF')
                AND (data_mart.metode_pembayaran = 'PP_PM' OR data_mart.metode_pembayaran = 'PP_CASH')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function DPFDfodCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW dpf_dfod_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = 'DPF')
            AND (
                (data_mart.metode_pembayaran ='CC_CASH')
                    AND
                (data_mart.klien_pengiriman IN ('ALWAHHIJAB', 'BLIBLIAPI', 'MAULAGI', 'TRIES', 'WEEKENDBGR', 'BITESHIP', NULL))
            )
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function DPFSuperCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW dpf_super_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = 'DPF')
            AND (data_mart.metode_pembayaran ='CC_CASH'
                    AND data_mart.klien_pengiriman IN ('SUPERINJND', 'SUPEROUT'))
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function DPFMPCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW dpf_mp_count_waybill AS
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
                    ".$schema.".data_mart dm
                WHERE (dm.kat = 'DPF')
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function DPFMPSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW dpf_mp_sum_biaya_kirim AS
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
                    ".$schema.".data_mart dm
                    WHERE (dm.kat = 'DPF')
                    GROUP BY
                        dm.drop_point_outgoing
                ) AS subquery";
    }

    public function DPFMPReturCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW dpf_mp_retur_count_waybill AS
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
                ".$schema.".data_mart dm
                WHERE (dm.kat = 'DPF')
                AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function DPFMPReturSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW dpf_mp_retur_sum_biaya_kirim AS
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
            ".$schema.".data_mart dm
            WHERE (dm.kat = 'DPF')
            AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
            GROUP BY
                dm.drop_point_outgoing
        ) AS subquery";
    }

    public function ZonasiAllCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW Zonasi_all_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing, COUNT(data_mart.no_waybill), SUM(data_mart.biaya_kirim)
                FROM ".$schema.".data_mart
                WHERE (data_mart.kat = 'ZONASI')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function ZonasiRegulerCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW Zonasi_reguler_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing,
                    count(data_mart.no_waybill) AS count,
                    sum(data_mart.biaya_kirim) AS sum
                    FROM ".$schema.".data_mart
                WHERE (data_mart.kat = 'ZONASI')
                AND (data_mart.metode_pembayaran = 'PP_PM' OR data_mart.metode_pembayaran = 'PP_CASH')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function ZonasiDfodCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW Zonasi_dfod_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = 'ZONASI')
            AND (data_mart.metode_pembayaran ='CC_CASH')
                    AND (data_mart.klien_pengiriman IN ('ALWAHHIJAB', 'BLIBLIAPI', 'MAULAGI', 'TRIES', 'WEEKENDBGR', 'BITESHIP', NULL))
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function ZonasiSuperCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW Zonasi_super_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = 'ZONASI')
            AND (data_mart.metode_pembayaran ='CC_CASH'
                    AND data_mart.klien_pengiriman IN ('SUPERINJND', 'SUPEROUT'))
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function ZonasiMPCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW Zonasi_mp_count_waybill AS
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
                    ".$schema.".data_mart dm
                WHERE (dm.kat = 'ZONASI')
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function ZonasiMPSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW Zonasi_mp_sum_biaya_kirim AS
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
                    ".$schema.".data_mart dm
                    WHERE (dm.kat = 'ZONASI')
                    GROUP BY
                        dm.drop_point_outgoing
                ) AS subquery";
    }

    public function ZonasiMPReturCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW Zonasi_mp_retur_count_waybill AS
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
                ".$schema.".data_mart dm
                WHERE (dm.kat = 'ZONASI')
                AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function ZonasiMPReturSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW Zonasi_mp_retur_sum_biaya_kirim AS
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
            ".$schema.".data_mart dm
            WHERE (dm.kat = 'ZONASI')
            AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
            GROUP BY
                dm.drop_point_outgoing
        ) AS subquery";
    }

    public function DCAllCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW dc_all_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing, COUNT(data_mart.no_waybill), SUM(data_mart.biaya_kirim)
                FROM ".$schema.".data_mart
                WHERE (data_mart.kat = 'DC')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function DCRegulerCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW dc_reguler_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing,
                    count(data_mart.no_waybill) AS count,
                    sum(data_mart.biaya_kirim) AS sum
                    FROM ".$schema.".data_mart
                WHERE (data_mart.kat = 'DC')
                AND (data_mart.metode_pembayaran = 'PP_PM' OR data_mart.metode_pembayaran = 'PP_CASH')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function DCDfodCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW dc_dfod_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = 'DC')
            AND ((data_mart.metode_pembayaran ='CC_CASH')
                    AND (data_mart.klien_pengiriman IN ('ALWAHHIJAB', 'BLIBLIAPI', 'MAULAGI', 'TRIES', 'WEEKENDBGR', 'BITESHIP', NULL)))
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function DCSuperCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW dc_super_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = 'DC')
            AND (data_mart.metode_pembayaran ='CC_CASH'
                    AND data_mart.klien_pengiriman IN ('SUPERINJND', 'SUPEROUT'))
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function DCMPCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW dc_mp_count_waybill AS
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
                    ".$schema.".data_mart dm
                WHERE (dm.kat = 'DC')
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function DCMPSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW dc_mp_sum_biaya_kirim AS
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
                    ".$schema.".data_mart dm
                    WHERE (dm.kat = 'DC')
                    GROUP BY
                        dm.drop_point_outgoing
                ) AS subquery";
    }

    public function DCMPReturCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW dc_mp_retur_count_waybill AS
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
                ".$schema.".data_mart dm
                WHERE (dm.kat = 'DC')
                AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function DCMPReturSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW dc_mp_retur_sum_biaya_kirim AS
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
            ".$schema.".data_mart dm
            WHERE (dm.kat = 'DC')
            AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
            GROUP BY
                dm.drop_point_outgoing
        ) AS subquery";
    }

    public function NAAllCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW na_all_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing, COUNT(data_mart.no_waybill), SUM(data_mart.biaya_kirim)
                FROM ".$schema.".data_mart
                WHERE (data_mart.kat = '#N/A')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function NARegulerCountSum($schema) {
        return "
            CREATE OR REPLACE VIEW na_reguler_count_sum AS
                SELECT DISTINCT data_mart.drop_point_outgoing,
                    count(data_mart.no_waybill) AS count,
                    sum(data_mart.biaya_kirim) AS sum
                    FROM ".$schema.".data_mart
                WHERE (data_mart.kat = '#N/A')
                AND (data_mart.metode_pembayaran = 'PP_PM' OR data_mart.metode_pembayaran = 'PP_CASH')
                GROUP BY data_mart.drop_point_outgoing";
    }

    public function NADfodCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW na_dfod_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = '#N/A')
            AND ((data_mart.metode_pembayaran ='CC_CASH')
                    AND (data_mart.klien_pengiriman IN ('ALWAHHIJAB', 'BLIBLIAPI', 'MAULAGI', 'TRIES', 'WEEKENDBGR', 'BITESHIP', NULL)))
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function NASuperCountSum($schema) {
        return "
        CREATE OR REPLACE VIEW na_super_count_sum AS
            SELECT DISTINCT data_mart.drop_point_outgoing,
                count(data_mart.no_waybill) AS count,
                sum(data_mart.biaya_kirim) AS SUM
            FROM ".$schema.".data_mart
            WHERE (data_mart.kat = '#N/A')
            AND (data_mart.metode_pembayaran ='CC_CASH'
                    AND data_mart.klien_pengiriman IN ('SUPERINJND', 'SUPEROUT'))
            GROUP BY data_mart.drop_point_outgoing";
    }

    public function NAMPCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW na_mp_count_waybill AS
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
                    ".$schema.".data_mart dm
                WHERE (dm.kat = '#N/A')
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function NAMPSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW na_mp_sum_biaya_kirim AS
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
                    ".$schema.".data_mart dm
                    WHERE (dm.kat = '#N/A')
                    GROUP BY
                        dm.drop_point_outgoing
                ) AS subquery";
    }

    public function NAMPReturCountWaybill($schema) {
        return "
        CREATE OR REPLACE VIEW na_mp_retur_count_waybill AS
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
                ".$schema.".data_mart dm
                WHERE (dm.kat = '#N/A')
                AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY
                    dm.drop_point_outgoing";
    }

    public function NAMPReturSumBiayaKirim($schema) {
        return "
        CREATE OR REPLACE VIEW na_mp_retur_sum_biaya_kirim AS
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
            ".$schema.".data_mart dm
            WHERE (dm.kat = '#N/A')
            AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
            GROUP BY
                dm.drop_point_outgoing
        ) AS subquery";
    }

    public function createViewRekapZonasi($schema) {
        return "
        CREATE OR REPLACE VIEW rekap_zonasi AS
        SELECT
            cp.kode_cp,
            zmpsbk.drop_point_outgoing,
            (
                COALESCE(zmpcw.bukaexpress, 0) +
                COALESCE(zmpcw.bukalapak, 0) +
                COALESCE(zmpcw.bukasend, 0)
            ) as awb_all_bukalapak,
            (
                COALESCE(zmpcw.shopee, 0) +
                COALESCE(zmpcw.shopee_cod, 0)
            ) as awb_all_shopee,
            (
                COALESCE(zmpcw.lazada, 0) +
                COALESCE(zmpcw.lazada_cod, 0)
            ) as awb_all_lazada,
            COALESCE(zmpcw.tokopedia, 0) as awb_tokopedia,
            (
                COALESCE(zmpcw.magellan, 0) +
                COALESCE(zmpcw.magellan_cod, 0)
            ) as awb_all_magellan,
            COALESCE(zmpcw.akulakuob, 0) as awb_akulakuob,
            COALESCE(zmpcw.ordivo, 0) as awb_ordivo,
            COALESCE(zmpcw.evermosapi, 0) as awb_evermosapi,
            (
                COALESCE(zmprcw.shopee, 0) +
                COALESCE(zmprcw.shopee_cod, 0)
            ) as awb_retur_all_shopee,
            (
                COALESCE(zmprcw.magellan, 0) +
                COALESCE(zmprcw.magellan_cod, 0)
            ) as awb_retur_all_magellan,
            (
                COALESCE(zmprcw.bukaexpress, 0) +
                COALESCE(zmprcw.bukalapak, 0) +
                COALESCE(zmprcw.bukasend, 0) +
                COALESCE(zmprcw.lazada, 0) +
                COALESCE(zmprcw.lazada_cod, 0) +
                COALESCE(zmprcw.tokopedia, 0) +
                COALESCE(zmprcw.akulakuob, 0) +
                COALESCE(zmprcw.ordivo, 0) +
                COALESCE(zmprcw.evermosapi, 0)
            ) as awb_retur_other_mp,
            (
                (
                COALESCE(zmpcw.bukaexpress, 0) +
                COALESCE(zmpcw.bukalapak, 0) +
                COALESCE(zmpcw.bukasend, 0) +
                COALESCE(zmpcw.shopee, 0) +
                COALESCE(zmpcw.shopee_cod, 0) +
                COALESCE(zmpcw.lazada, 0) +
                COALESCE(zmpcw.lazada_cod, 0) +
                COALESCE(zmpcw.tokopedia, 0) +
                COALESCE(zmpcw.magellan, 0) +
                COALESCE(zmpcw.magellan_cod, 0) +
                COALESCE(zmpcw.akulakuob, 0) +
                COALESCE(zmpcw.ordivo, 0) +
                COALESCE(zmpcw.evermosapi, 0)
                ) -
                (
                COALESCE(zmprcw.bukaexpress, 0) +
                COALESCE(zmprcw.bukalapak, 0) +
                COALESCE(zmprcw.bukasend, 0) +
                COALESCE(zmprcw.lazada, 0) +
                COALESCE(zmprcw.lazada_cod, 0) +
                COALESCE(zmprcw.tokopedia, 0) +
                COALESCE(zmprcw.akulakuob, 0) +
                COALESCE(zmprcw.ordivo, 0) +
                COALESCE(zmprcw.evermosapi, 0)
                )
            ) as total_awb,
            (
                ((
                COALESCE(zmpcw.bukaexpress, 0) +
                COALESCE(zmpcw.bukalapak, 0) +
                COALESCE(zmpcw.bukasend, 0) +
                COALESCE(zmpcw.shopee, 0) +
                COALESCE(zmpcw.shopee_cod, 0) +
                COALESCE(zmpcw.lazada, 0) +
                COALESCE(zmpcw.lazada_cod, 0) +
                COALESCE(zmpcw.tokopedia, 0) +
                COALESCE(zmpcw.magellan, 0) +
                COALESCE(zmpcw.magellan_cod, 0) +
                COALESCE(zmpcw.akulakuob, 0) +
                COALESCE(zmpcw.ordivo, 0) +
                COALESCE(zmpcw.evermosapi, 0)
                ) -
                (
                COALESCE(zmprcw.bukaexpress, 0) +
                COALESCE(zmprcw.bukalapak, 0) +
                COALESCE(zmprcw.bukasend, 0) +
                COALESCE(zmprcw.lazada, 0) +
                COALESCE(zmprcw.lazada_cod, 0) +
                COALESCE(zmprcw.tokopedia, 0) +
                COALESCE(zmprcw.akulakuob, 0) +
                COALESCE(zmprcw.ordivo, 0) +
                COALESCE(zmprcw.evermosapi, 0)
                )) * 750
            ) as discount_awb,
            (
                CAST(
                    ROUND(
                    (
                    (
                        (
                            COALESCE(zmpcw.bukaexpress, 0) +
                            COALESCE(zmpcw.bukalapak, 0) +
                            COALESCE(zmpcw.bukasend, 0) +
                            COALESCE(zmpcw.shopee, 0) +
                            COALESCE(zmpcw.shopee_cod, 0) +
                            COALESCE(zmpcw.lazada, 0) +
                            COALESCE(zmpcw.lazada_cod, 0) +
                            COALESCE(zmpcw.tokopedia, 0) +
                            COALESCE(zmpcw.magellan, 0) +
                            COALESCE(zmpcw.magellan_cod, 0) +
                            COALESCE(zmpcw.akulakuob, 0) +
                            COALESCE(zmpcw.ordivo, 0) +
                            COALESCE(zmpcw.evermosapi, 0)
                        ) -
                            (
                            COALESCE(zmprcw.bukaexpress, 0) +
                            COALESCE(zmprcw.bukalapak, 0) +
                            COALESCE(zmprcw.bukasend, 0) +
                            COALESCE(zmprcw.lazada, 0) +
                            COALESCE(zmprcw.lazada_cod, 0) +
                            COALESCE(zmprcw.tokopedia, 0) +
                            COALESCE(zmprcw.akulakuob, 0) +
                            COALESCE(zmprcw.ordivo, 0) +
                            COALESCE(zmprcw.evermosapi, 0)
                        )
                    ) * 750
                    ) * 0.011 )
                AS BIGINT )
            ) as ppn_11,
            (
                CAST(
                    ROUND(
                            (
                        (
                        (
                            COALESCE(zmpcw.bukaexpress, 0) +
                            COALESCE(zmpcw.bukalapak, 0) +
                            COALESCE(zmpcw.bukasend, 0) +
                            COALESCE(zmpcw.shopee, 0) +
                            COALESCE(zmpcw.shopee_cod, 0) +
                            COALESCE(zmpcw.lazada, 0) +
                            COALESCE(zmpcw.lazada_cod, 0) +
                            COALESCE(zmpcw.tokopedia, 0) +
                            COALESCE(zmpcw.magellan, 0) +
                            COALESCE(zmpcw.magellan_cod, 0) +
                            COALESCE(zmpcw.akulakuob, 0) +
                            COALESCE(zmpcw.ordivo, 0) +
                            COALESCE(zmpcw.evermosapi, 0)
                        ) -
                            (
                            COALESCE(zmprcw.bukaexpress, 0) +
                            COALESCE(zmprcw.bukalapak, 0) +
                            COALESCE(zmprcw.bukasend, 0) +
                            COALESCE(zmprcw.lazada, 0) +
                            COALESCE(zmprcw.lazada_cod, 0) +
                            COALESCE(zmprcw.tokopedia, 0) +
                            COALESCE(zmprcw.akulakuob, 0) +
                            COALESCE(zmprcw.ordivo, 0) +
                            COALESCE(zmprcw.evermosapi, 0)
                        )
                    ) * 750
                    ) -
                    (
                    (
                        (
                            COALESCE(zmpcw.bukaexpress, 0) +
                            COALESCE(zmpcw.bukalapak, 0) +
                            COALESCE(zmpcw.bukasend, 0) +
                            COALESCE(zmpcw.shopee, 0) +
                            COALESCE(zmpcw.shopee_cod, 0) +
                            COALESCE(zmpcw.lazada, 0) +
                            COALESCE(zmpcw.lazada_cod, 0) +
                            COALESCE(zmpcw.tokopedia, 0) +
                            COALESCE(zmpcw.magellan, 0) +
                            COALESCE(zmpcw.magellan_cod, 0) +
                            COALESCE(zmpcw.akulakuob, 0) +
                            COALESCE(zmpcw.ordivo, 0) +
                            COALESCE(zmpcw.evermosapi, 0)
                        ) -
                            (
                            COALESCE(zmprcw.bukaexpress, 0) +
                            COALESCE(zmprcw.bukalapak, 0) +
                            COALESCE(zmprcw.bukasend, 0) +
                            COALESCE(zmprcw.lazada, 0) +
                            COALESCE(zmprcw.lazada_cod, 0) +
                            COALESCE(zmprcw.tokopedia, 0) +
                            COALESCE(zmprcw.akulakuob, 0) +
                            COALESCE(zmprcw.ordivo, 0) +
                            COALESCE(zmprcw.evermosapi, 0)
                        )
                    ) * 750
                    ) * 0.011 )
                AS BIGINT )
            ) as total_cashback_marketplace
            FROM ".$schema.".zonasi_mp_sum_biaya_kirim zmpsbk
            LEFT JOIN ".$schema.".zonasi_mp_count_waybill zmpcw ON zmpsbk.drop_point_outgoing = zmpcw.drop_point_outgoing
            LEFT JOIN ".$schema.".zonasi_mp_retur_count_waybill zmprcw ON zmpsbk.drop_point_outgoing = zmprcw.drop_point_outgoing
            LEFT JOIN master_collection_point cp ON zmpsbk.drop_point_outgoing = cp.nama_cp
        ";
    }

    public function createViewGradingA($schema) {
        return "
        CREATE OR REPLACE VIEW cp_dp_raw_grading_1 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                COALESCE(acs.sum, 0) AS biaya_kirim_all,
                COALESCE(rcs.sum, 0) AS biaya_kirim_reguler,
                COALESCE(dcs.sum, 0) AS biaya_kirim_dfod,
                COALESCE(scs.sum, 0) AS biaya_kirim_super,
                (
                    COALESCE(rcs.sum, 0) +
                    COALESCE(dcs.sum, 0) +
                    COALESCE(scs.sum, 0)
                ) AS total_biaya_kirim,
                CAST(
                    ROUND(
                        COALESCE(rcs.sum, 0) +
                        COALESCE(dcs.sum, 0) +
                        COALESCE(scs.sum, 0) /
                        1.011
                    )::BIGINT AS BIGINT
                ) AS total_biaya_kirim_dikurangi_ppn,
                CAST(
                    ROUND(
                            (
                                (
                                    COALESCE(rcs.sum, 0) +
                                    COALESCE(dcs.sum, 0) +
                                    COALESCE(scs.sum, 0)
                                ) / 1.011
                        ) * 0.25
                    )::BIGINT AS BIGINT
                ) AS amount_discount_25,
                --reguler
                COALESCE(sbk.akulakuob, 0) AS akulaku,
                COALESCE(sbk.ordivo, 0) AS ordivo,
                COALESCE(sbk.evermosapi, 0) AS evermos,
                COALESCE(sbk.mengantar, 0) AS mengantar,
                (
                    COALESCE(sbk.akulakuob, 0) +
                    COALESCE(sbk.ordivo, 0) +
                    COALESCE(sbk.evermosapi, 0) +
                    COALESCE(sbk.mengantar, 0)
                ) AS total_biaya_kirim_a,
                CAST(
                    ROUND(
                        (
                            COALESCE(sbk.akulakuob, 0) +
                            COALESCE(sbk.ordivo, 0) +
                            COALESCE(sbk.evermosapi, 0) +
                            COALESCE(sbk.mengantar, 0)
                        ) / 1.011
                    )::BIGINT AS BIGINT
                ) AS total_biaya_kirim_a_dikurangi_ppn,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(sbk.akulakuob, 0) +
                                COALESCE(sbk.ordivo, 0) +
                                COALESCE(sbk.evermosapi, 0) +
                                COALESCE(sbk.mengantar, 0)
                            )/ 1.011
                    ) * 0.10
                    )::BIGINT AS BIGINT
                ) AS amount_discount_10,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(rcs.sum, 0) +
                                COALESCE(dcs.sum, 0) +
                                COALESCE(scs.sum, 0)
                            ) / 1.011
                        ) * 0.25 +
                        (
                            (
                                COALESCE(sbk.akulakuob, 0) +
                                COALESCE(sbk.ordivo, 0) +
                                COALESCE(sbk.evermosapi, 0) +
                                COALESCE(sbk.mengantar, 0)
                            ) / 1.011
                        ) * 0.10
                    )::BIGINT AS BIGINT
                ) AS total_cashback_reguler,
                --marketplace
                (
                    COALESCE(sbk.bukalapak, 0) +
                    COALESCE(sbk.bukaexpress, 0) +
                    COALESCE(sbk.bukasend, 0)
                ) AS bukalapak,
                (
                    COALESCE(sbk.bukalapak, 0) +
                    COALESCE(sbk.bukaexpress, 0) +
                    COALESCE(sbk.bukasend, 0)
                ) AS total_biaya_kirim_bukalapak,
                CAST(
                    ROUND(
                        (
                            COALESCE(sbk.bukalapak, 0) +
                            COALESCE(sbk.bukaexpress, 0) +
                            COALESCE(sbk.bukasend, 0)
                        ) / 1.011
                    ) AS BIGINT
                ) AS total_biaya_kirim_bukalapak_dikurangi_ppn,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(sbk.bukalapak, 0) +
                                COALESCE(sbk.bukaexpress, 0) +
                                COALESCE(sbk.bukasend, 0)
                            ) / 1.011
                        ) * 0.05
                    ) AS BIGINT
                ) AS discount_bukalapak_5,
                COALESCE(sbk.shopee_cod, 0) AS shopee_cod,
                COALESCE(srbk.shopee_cod, 0) AS retur_shopee_cod,
                (
                    COALESCE(sbk.shopee_cod, 0) -
                    COALESCE(srbk.shopee_cod, 0)
                ) AS total_biaya_kirim_shopee_cod,
                COALESCE(sbk.magellan_cod, 0) AS magellan_cod,
                COALESCE(srbk.magellan_cod, 0) AS retur_magellan_cod,
                (
                    COALESCE(sbk.magellan_cod, 0) -
                    COALESCE(srbk.magellan_cod, 0)
                ) AS total_biaya_kirim_magellan_cod,
                COALESCE(sbk.lazada_cod, 0) AS lazada_cod,
                COALESCE(srbk.lazada_cod, 0) AS retur_lazada_cod,
                (
                    COALESCE(sbk.lazada_cod, 0) -
                    COALESCE(srbk.lazada_cod, 0)
                ) AS total_biaya_kirim_lazada_cod,
                CAST(
                    ROUND(
                        (
                            COALESCE(sbk.shopee_cod, 0) -
                            COALESCE(srbk.shopee_cod, 0)
                        ) +
                        (
                            COALESCE(sbk.magellan_cod, 0) -
                            COALESCE(srbk.magellan_cod, 0)
                        ) +
                        (
                            COALESCE(sbk.lazada_cod, 0) -
                            COALESCE(srbk.lazada_cod, 0)
                        )
                    ) AS BIGINT
                ) AS total_biaya_kirim_cod,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(sbk.shopee_cod, 0) -
                                COALESCE(srbk.shopee_cod, 0)
                            ) +
                            (
                                COALESCE(sbk.magellan_cod, 0) -
                                COALESCE(srbk.magellan_cod, 0)
                            ) +
                            (
                                COALESCE(sbk.lazada_cod, 0) -
                                COALESCE(srbk.lazada_cod, 0)
                            )
                        ) / 1.011
                    ) AS BIGINT
                ) AS total_biaya_kirim_cod_dikurangi_ppn,
                CAST(
                    ROUND(
                        (
                            (
                                (
                                    COALESCE(sbk.shopee_cod, 0) -
                                    COALESCE(srbk.shopee_cod, 0)
                                ) +
                                (
                                    COALESCE(sbk.magellan_cod, 0) -
                                    COALESCE(srbk.magellan_cod, 0)
                                ) +
                                (
                                    COALESCE(sbk.lazada_cod, 0) -
                                    COALESCE(srbk.lazada_cod, 0)
                                )
                            ) / 1.011
                        ) * 0.07
                    ) AS BIGINT
                ) AS diskon_cod_7,
                COALESCE(sbk.tokopedia, 0) AS tokopedia,
                COALESCE(sbk.tokopedia, 0) AS total_biaya_kirim_tokopedia,
                CAST(
                    ROUND(
                        COALESCE(sbk.tokopedia, 0) / 1.011
                    ) AS BIGINT
                ) AS total_biaya_kirim_tokopedia_dikurangi_ppn,
                CAST(
                    ROUND(
                        (
                            COALESCE(sbk.tokopedia, 0) / 1.011
                        ) * 0.1
                    ) AS BIGINT
                ) AS discount_tokopedia_10,
                CAST(
                    (
                        ROUND(
                            (
                                (
                                    COALESCE(sbk.bukalapak, 0) +
                                    COALESCE(sbk.bukaexpress, 0) +
                                    COALESCE(sbk.bukasend, 0)
                                ) / 1.011
                            ) * 0.05
                        ) +
                        ROUND(
                            (
                                (
                                    (
                                        COALESCE(sbk.shopee_cod, 0) -
                                        COALESCE(srbk.shopee_cod, 0)
                                    ) +
                                    (
                                        COALESCE(sbk.magellan_cod, 0) -
                                        COALESCE(srbk.magellan_cod, 0)
                                    ) +
                                    (
                                        COALESCE(sbk.lazada_cod, 0) -
                                        COALESCE(srbk.lazada_cod, 0)
                                    )
                                ) / 1.011
                            ) * 0.07
                        ) +
                        ROUND(
                            (
                                COALESCE(sbk.tokopedia, 0) / 1.011
                            ) * 0.1
                        )
                    ) AS BIGINT
                ) AS cashback_marketplace,
                --non-cod
                COALESCE(sbk.lazada, 0) AS lazada,
                COALESCE(srbk.lazada, 0) AS retur_lazada,
                COALESCE(sbk.shopee, 0) AS shopee,
                COALESCE(srbk.shopee, 0) AS retur_shopee,
                --tokotalk 0
                COALESCE(sbk.magellan, 0) AS magellan,
                COALESCE(srbk.magellan, 0) AS retur_magellan,
                (
                    COALESCE(srbk.akulakuob, 0) +
                    COALESCE(srbk.bukaexpress, 0) +
                    COALESCE(srbk.evermosapi, 0) +
                    COALESCE(srbk.mengantar, 0) +
                    COALESCE(srbk.ordivo, 0) +
                    COALESCE(srbk.tokopedia, 0)
                ) AS total_retur_pilihan,
                (
                    (COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0)) +
                    (COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0)) +
                    (COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0)) +
                    (
                        COALESCE(srbk.akulakuob, 0) +
                        COALESCE(srbk.bukaexpress, 0) +
                        COALESCE(srbk.evermosapi, 0) +
                        COALESCE(srbk.mengantar, 0) +
                        COALESCE(srbk.ordivo, 0) +
                        COALESCE(srbk.tokopedia, 0)
                    ) +
                    0 --retur belum terpotong
                ) AS total_biaya_kirim_non_cod,
                (
                CAST(ROUND((
                        (COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0)) +
                        (COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0)) +
                        (COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0)) +
                        (
                            COALESCE(srbk.akulakuob, 0) +
                            COALESCE(srbk.bukaexpress, 0) +
                            COALESCE(srbk.evermosapi, 0) +
                            COALESCE(srbk.mengantar, 0) +
                            COALESCE(srbk.ordivo, 0) +
                            COALESCE(srbk.tokopedia, 0)
                        ) +
                        0 --retur belum terpotong
                    ) / 1.011) AS BIGINT)) AS total_biaya_kirim_non_cod_dikurangi_ppn,
                    (
                CAST(
                    ROUND(
                    (
                            (COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0)) +
                            (COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0)) +
                            (COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0)) +
                            (
                                COALESCE(srbk.akulakuob, 0) +
                                COALESCE(srbk.bukaexpress, 0) +
                                COALESCE(srbk.evermosapi, 0) +
                                COALESCE(srbk.mengantar, 0) +
                                COALESCE(srbk.ordivo, 0) +
                                COALESCE(srbk.tokopedia, 0)
                            ) +
                            0 --retur belum terpotong
                        ) / 1.011
                    ) * 0.09 AS BIGINT)) AS discount_total_biaya_kirim_9,
                    (	CAST(
                        (
                            ROUND(
                                (
                                    (
                                        COALESCE(sbk.bukalapak, 0) +
                                        COALESCE(sbk.bukaexpress, 0) +
                                        COALESCE(sbk.bukasend, 0)
                                    ) / 1.011
                                ) * 0.05
                            ) +
                            ROUND(
                                (
                                    (
                                        (
                                            COALESCE(sbk.shopee_cod, 0) -
                                            COALESCE(srbk.shopee_cod, 0)
                                        ) +
                                        (
                                            COALESCE(sbk.magellan_cod, 0) -
                                            COALESCE(srbk.magellan_cod, 0)
                                        ) +
                                        (
                                            COALESCE(sbk.lazada_cod, 0) -
                                            COALESCE(srbk.lazada_cod, 0)
                                        )
                                    ) / 1.011
                                ) * 0.07
                            ) +
                            ROUND(
                                (
                                    COALESCE(sbk.tokopedia, 0) / 1.011
                                ) * 0.1
                            )
                        ) AS BIGINT
                    ) +
                    CAST(
                        ROUND(
                        (
                                (COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0)) +
                                (COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0)) +
                                (COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0)) +
                                (
                                    COALESCE(srbk.akulakuob, 0) +
                                    COALESCE(srbk.bukaexpress, 0) +
                                    COALESCE(srbk.evermosapi, 0) +
                                    COALESCE(srbk.mengantar, 0) +
                                    COALESCE(srbk.ordivo, 0) +
                                    COALESCE(srbk.tokopedia, 0)
                                ) +
                                0 --retur belum terpotong
                            ) / 1.011
                        ) * 0.09 AS BIGINT)
                ) AS total_cashback_marketplace,
                COALESCE(rzmp.total_cashback_marketplace, 0) AS total_cashback_luar_zona,
                (
                    (
                        CAST(
                            ROUND(
                                (
                                    (
                                        COALESCE(rcs.sum, 0) +
                                        COALESCE(dcs.sum, 0) +
                                        COALESCE(scs.sum, 0)
                                    ) / 1.011
                                ) * 0.25 +
                                (
                                    (
                                        COALESCE(sbk.akulakuob, 0) +
                                        COALESCE(sbk.ordivo, 0) +
                                        COALESCE(sbk.evermosapi, 0) +
                                        COALESCE(sbk.mengantar, 0)
                                    ) / 1.011
                                ) * 0.10
                            )::BIGINT AS BIGINT
                        )
                    ) +
                    (	CAST(
                            (
                                ROUND(
                                    (
                                        (
                                            COALESCE(sbk.bukalapak, 0) +
                                            COALESCE(sbk.bukaexpress, 0) +
                                            COALESCE(sbk.bukasend, 0)
                                        ) / 1.011
                                    ) * 0.05
                                ) +
                                ROUND(
                                    (
                                        (
                                            (
                                                COALESCE(sbk.shopee_cod, 0) -
                                                COALESCE(srbk.shopee_cod, 0)
                                            ) +
                                            (
                                                COALESCE(sbk.magellan_cod, 0) -
                                                COALESCE(srbk.magellan_cod, 0)
                                            ) +
                                            (
                                                COALESCE(sbk.lazada_cod, 0) -
                                                COALESCE(srbk.lazada_cod, 0)
                                            )
                                        ) / 1.011
                                    ) * 0.07
                                ) +
                                ROUND(
                                    (
                                        COALESCE(sbk.tokopedia, 0) / 1.011
                                    ) * 0.1
                                )
                            ) AS BIGINT
                        ) +
                        CAST(
                            ROUND(
                            (
                                    (COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0)) +
                                    (COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0)) +
                                    (COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0)) +
                                    (
                                        COALESCE(srbk.akulakuob, 0) +
                                        COALESCE(srbk.bukaexpress, 0) +
                                        COALESCE(srbk.evermosapi, 0) +
                                        COALESCE(srbk.mengantar, 0) +
                                        COALESCE(srbk.ordivo, 0) +
                                        COALESCE(srbk.tokopedia, 0)
                                    ) +
                                    0 --retur belum terpotong
                                ) / 1.011
                            ) * 0.09 AS BIGINT)
                    ) +
                    (
                        COALESCE(rzmp.total_cashback_marketplace, 0)
                    )
                ) AS total_cashback
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_all_count_sum AS acs ON cp.drop_point_outgoing = acs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_reguler_count_sum AS rcs ON cp.drop_point_outgoing = rcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_dfod_count_sum AS dcs ON cp.drop_point_outgoing = dcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_super_count_sum AS scs ON cp.drop_point_outgoing = scs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".rekap_zonasi AS rzmp ON cp.drop_point_outgoing = rzmp.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'A'
        ";
    }

    public function createViewCPDPCashbackRegulerGrading1($schema) {
        return "
            CREATE OR REPLACE VIEW cp_dp_cashback_reguler_grading_1 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                COALESCE(acs.sum, 0) AS biaya_kirim_all,
                COALESCE(rcs.sum, 0) AS biaya_kirim_reguler,
                COALESCE(dcs.sum, 0) AS biaya_kirim_dfod,
                COALESCE(scs.sum, 0) AS biaya_kirim_super,
                (
                    COALESCE(rcs.sum, 0) +
                    COALESCE(dcs.sum, 0) +
                    COALESCE(scs.sum, 0)
                ) AS total_biaya_kirim,
                CAST(
                    ROUND(
                        COALESCE(rcs.sum, 0) +
                        COALESCE(dcs.sum, 0) +
                        COALESCE(scs.sum, 0) /
                        1.011
                    )::BIGINT AS BIGINT
                ) AS total_biaya_kirim_dikurangi_ppn,
                CAST(
                    ROUND(
                            (
                                (
                                    COALESCE(rcs.sum, 0) +
                                    COALESCE(dcs.sum, 0) +
                                    COALESCE(scs.sum, 0)
                                ) / 1.011
                        ) * 0.25
                    )::BIGINT AS BIGINT
                ) AS amount_discount_25,
                --reguler
                COALESCE(sbk.akulakuob, 0) AS akulaku,
                COALESCE(sbk.ordivo, 0) AS ordivo,
                COALESCE(sbk.evermosapi, 0) AS evermos,
                COALESCE(sbk.mengantar, 0) AS mengantar,
                (
                    COALESCE(sbk.akulakuob, 0) +
                    COALESCE(sbk.ordivo, 0) +
                    COALESCE(sbk.evermosapi, 0) +
                    COALESCE(sbk.mengantar, 0)
                ) AS total_biaya_kirim_a,
                CAST(
                    ROUND(
                        (
                            COALESCE(sbk.akulakuob, 0) +
                            COALESCE(sbk.ordivo, 0) +
                            COALESCE(sbk.evermosapi, 0) +
                            COALESCE(sbk.mengantar, 0)
                        ) / 1.011
                    )::BIGINT AS BIGINT
                ) AS total_biaya_kirim_a_dikurangi_ppn,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(sbk.akulakuob, 0) +
                                COALESCE(sbk.ordivo, 0) +
                                COALESCE(sbk.evermosapi, 0) +
                                COALESCE(sbk.mengantar, 0)
                            )/ 1.011
                    ) * 0.10
                    )::BIGINT AS BIGINT
                ) AS amount_discount_10,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(rcs.sum, 0) +
                                COALESCE(dcs.sum, 0) +
                                COALESCE(scs.sum, 0)
                            ) / 1.011
                        ) * 0.25 +
                        (
                            (
                                COALESCE(sbk.akulakuob, 0) +
                                COALESCE(sbk.ordivo, 0) +
                                COALESCE(sbk.evermosapi, 0) +
                                COALESCE(sbk.mengantar, 0)
                            ) / 1.011
                        ) * 0.10
                    )::BIGINT AS BIGINT
                ) AS total_cashback_reguler
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_all_count_sum AS acs ON cp.drop_point_outgoing = acs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_reguler_count_sum AS rcs ON cp.drop_point_outgoing = rcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_dfod_count_sum AS dcs ON cp.drop_point_outgoing = dcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_super_count_sum AS scs ON cp.drop_point_outgoing = scs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".rekap_zonasi AS rzmp ON cp.drop_point_outgoing = rzmp.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'A'
        ";
    }

    public function createViewCPDPCashbackRegulerGrading2($schema) {
        return "
        CREATE OR REPLACE VIEW cp_dp_cashback_reguler_grading_2 AS
        SELECT
            cp.kode_cp,
            cp.nama_cp,
            COALESCE(acs.sum, 0) AS biaya_kirim_all,
            COALESCE(rcs.sum, 0) AS biaya_kirim_reguler,
            COALESCE(dcs.sum, 0) AS biaya_kirim_dfod,
            COALESCE(scs.sum, 0) AS biaya_kirim_super,
            (
                COALESCE(rcs.sum, 0) +
                COALESCE(dcs.sum, 0) +
                COALESCE(scs.sum, 0)
            ) AS total_biaya_kirim,
            CAST(
                ROUND(
                    COALESCE(rcs.sum, 0) +
                    COALESCE(dcs.sum, 0) +
                    COALESCE(scs.sum, 0) /
                    1.011
                )::BIGINT AS BIGINT
            ) AS total_biaya_kirim_dikurangi_ppn,
            CAST(
                ROUND(
                        (
                            (
                                COALESCE(rcs.sum, 0) +
                                COALESCE(dcs.sum, 0) +
                                COALESCE(scs.sum, 0)
                            ) / 1.011
                    ) * 0.25
                )::BIGINT AS BIGINT
            ) AS amount_discount_25,
            --reguler
            COALESCE(sbk.akulakuob, 0) AS akulaku,
            COALESCE(sbk.ordivo, 0) AS ordivo,
            COALESCE(sbk.evermosapi, 0) AS evermos,
            COALESCE(sbk.mengantar, 0) AS mengantar,
            (
                COALESCE(sbk.akulakuob, 0) +
                COALESCE(sbk.ordivo, 0) +
                COALESCE(sbk.evermosapi, 0) +
                COALESCE(sbk.mengantar, 0)
            ) AS total_biaya_kirim_a,
            CAST(
                ROUND(
                    (
                        COALESCE(sbk.akulakuob, 0) +
                        COALESCE(sbk.ordivo, 0) +
                        COALESCE(sbk.evermosapi, 0) +
                        COALESCE(sbk.mengantar, 0)
                    ) / 1.011
                )::BIGINT AS BIGINT
            ) AS total_biaya_kirim_a_dikurangi_ppn,
            CAST(
                ROUND(
                    (
                        (
                            COALESCE(sbk.akulakuob, 0) +
                            COALESCE(sbk.ordivo, 0) +
                            COALESCE(sbk.evermosapi, 0) +
                            COALESCE(sbk.mengantar, 0)
                        )/ 1.011
                ) * 0.10
                )::BIGINT AS BIGINT
            ) AS amount_discount_10,
            CAST(
                ROUND(
                    (
                        (
                            COALESCE(rcs.sum, 0) +
                            COALESCE(dcs.sum, 0) +
                            COALESCE(scs.sum, 0)
                        ) / 1.011
                    ) * 0.25 +
                    (
                        (
                            COALESCE(sbk.akulakuob, 0) +
                            COALESCE(sbk.ordivo, 0) +
                            COALESCE(sbk.evermosapi, 0) +
                            COALESCE(sbk.mengantar, 0)
                        ) / 1.011
                    ) * 0.10
                )::BIGINT AS BIGINT
            ) AS total_cashback_reguler
        FROM
            PUBLIC.master_collection_point AS cp
        LEFT JOIN
            ".$schema.".cp_dp_all_count_sum AS acs ON cp.drop_point_outgoing = acs.drop_point_outgoing
        LEFT JOIN
            ".$schema.".cp_dp_reguler_count_sum AS rcs ON cp.drop_point_outgoing = rcs.drop_point_outgoing
        LEFT JOIN
            ".$schema.".cp_dp_dfod_count_sum AS dcs ON cp.drop_point_outgoing = dcs.drop_point_outgoing
        LEFT JOIN
            ".$schema.".cp_dp_super_count_sum AS scs ON cp.drop_point_outgoing = scs.drop_point_outgoing
        LEFT JOIN
            ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
        LEFT JOIN
            ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
        LEFT JOIN
            ".$schema.".rekap_zonasi AS rzmp ON cp.drop_point_outgoing = rzmp.drop_point_outgoing
        WHERE
            cp.grading_pickup = 'B'
        ";
    }

    public function createViewCPDPCashbackRegulerGrading3($schema) {
        return "
            CREATE OR REPLACE VIEW cp_dp_cashback_reguler_grading_3 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                COALESCE(acs.sum, 0) AS biaya_kirim_all,
                COALESCE(rcs.sum, 0) AS biaya_kirim_reguler,
                COALESCE(dcs.sum, 0) AS biaya_kirim_dfod,
                COALESCE(scs.sum, 0) AS biaya_kirim_super,
                (
                    COALESCE(rcs.sum, 0) +
                    COALESCE(dcs.sum, 0) +
                    COALESCE(scs.sum, 0)
                ) AS total_biaya_kirim,
                CAST(
                    ROUND(
                        COALESCE(rcs.sum, 0) +
                        COALESCE(dcs.sum, 0) +
                        COALESCE(scs.sum, 0) /
                        1.011
                    )::BIGINT AS BIGINT
                ) AS total_biaya_kirim_dikurangi_ppn,
                CAST(
                    ROUND(
                            (
                                (
                                    COALESCE(rcs.sum, 0) +
                                    COALESCE(dcs.sum, 0) +
                                    COALESCE(scs.sum, 0)
                                ) / 1.011
                        ) * 0.25
                    )::BIGINT AS BIGINT
                ) AS amount_discount_25,
                --reguler
                COALESCE(sbk.akulakuob, 0) AS akulaku,
                COALESCE(sbk.ordivo, 0) AS ordivo,
                COALESCE(sbk.evermosapi, 0) AS evermos,
                COALESCE(sbk.mengantar, 0) AS mengantar,
                (
                    COALESCE(sbk.akulakuob, 0) +
                    COALESCE(sbk.ordivo, 0) +
                    COALESCE(sbk.evermosapi, 0) +
                    COALESCE(sbk.mengantar, 0)
                ) AS total_biaya_kirim_a,
                CAST(
                    ROUND(
                        (
                            COALESCE(sbk.akulakuob, 0) +
                            COALESCE(sbk.ordivo, 0) +
                            COALESCE(sbk.evermosapi, 0) +
                            COALESCE(sbk.mengantar, 0)
                        ) / 1.011
                    )::BIGINT AS BIGINT
                ) AS total_biaya_kirim_a_dikurangi_ppn,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(sbk.akulakuob, 0) +
                                COALESCE(sbk.ordivo, 0) +
                                COALESCE(sbk.evermosapi, 0) +
                                COALESCE(sbk.mengantar, 0)
                            )/ 1.011
                    ) * 0.10
                    )::BIGINT AS BIGINT
                ) AS amount_discount_10,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(rcs.sum, 0) +
                                COALESCE(dcs.sum, 0) +
                                COALESCE(scs.sum, 0)
                            ) / 1.011
                        ) * 0.25 +
                        (
                            (
                                COALESCE(sbk.akulakuob, 0) +
                                COALESCE(sbk.ordivo, 0) +
                                COALESCE(sbk.evermosapi, 0) +
                                COALESCE(sbk.mengantar, 0)
                            ) / 1.011
                        ) * 0.10
                    )::BIGINT AS BIGINT
                ) AS total_cashback_reguler
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_all_count_sum AS acs ON cp.drop_point_outgoing = acs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_reguler_count_sum AS rcs ON cp.drop_point_outgoing = rcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_dfod_count_sum AS dcs ON cp.drop_point_outgoing = dcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_super_count_sum AS scs ON cp.drop_point_outgoing = scs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".rekap_zonasi AS rzmp ON cp.drop_point_outgoing = rzmp.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'C'
        ";
    }

    public function createViewCPDPCashbackCODGrading1($schema) {
        return "
            CREATE OR REPLACE VIEW cp_dp_cashback_cod_grading_1 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                (
                    COALESCE(sbk.bukalapak, 0) +
                    COALESCE(sbk.bukaexpress, 0) +
                    COALESCE(sbk.bukasend, 0)
                ) AS bukalapak,
                (
                    COALESCE(sbk.bukalapak, 0) +
                    COALESCE(sbk.bukaexpress, 0) +
                    COALESCE(sbk.bukasend, 0)
                ) AS total_biaya_kirim_bukalapak,
                CAST(
                    ROUND(
                        (
                            COALESCE(sbk.bukalapak, 0) +
                            COALESCE(sbk.bukaexpress, 0) +
                            COALESCE(sbk.bukasend, 0)
                        ) / 1.011
                    ) AS BIGINT
                ) AS total_biaya_kirim_bukalapak_dikurangi_ppn,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(sbk.bukalapak, 0) +
                                COALESCE(sbk.bukaexpress, 0) +
                                COALESCE(sbk.bukasend, 0)
                            ) / 1.011
                        ) * 0.05
                    ) AS BIGINT
                ) AS discount_bukalapak_5,
                COALESCE(sbk.shopee_cod, 0) AS shopee_cod,
                COALESCE(srbk.shopee_cod, 0) AS retur_shopee_cod,
                (
                    COALESCE(sbk.shopee_cod, 0) -
                    COALESCE(srbk.shopee_cod, 0)
                ) AS total_biaya_kirim_shopee_cod,
                COALESCE(sbk.magellan_cod, 0) AS magellan_cod,
                COALESCE(srbk.magellan_cod, 0) AS retur_magellan_cod,
                (
                    COALESCE(sbk.magellan_cod, 0) -
                    COALESCE(srbk.magellan_cod, 0)
                ) AS total_biaya_kirim_magellan_cod,
                COALESCE(sbk.lazada_cod, 0) AS lazada_cod,
                COALESCE(srbk.lazada_cod, 0) AS retur_lazada_cod,
                (
                    COALESCE(sbk.lazada_cod, 0) -
                    COALESCE(srbk.lazada_cod, 0)
                ) AS total_biaya_kirim_lazada_cod,
                CAST(
                    ROUND(
                        (
                            COALESCE(sbk.shopee_cod, 0) -
                            COALESCE(srbk.shopee_cod, 0)
                        ) +
                        (
                            COALESCE(sbk.magellan_cod, 0) -
                            COALESCE(srbk.magellan_cod, 0)
                        ) +
                        (
                            COALESCE(sbk.lazada_cod, 0) -
                            COALESCE(srbk.lazada_cod, 0)
                        )
                    ) AS BIGINT
                ) AS total_biaya_kirim_cod,
                CAST(
                    ROUND(
                        (
                            (
                                COALESCE(sbk.shopee_cod, 0) -
                                COALESCE(srbk.shopee_cod, 0)
                            ) +
                            (
                                COALESCE(sbk.magellan_cod, 0) -
                                COALESCE(srbk.magellan_cod, 0)
                            ) +
                            (
                                COALESCE(sbk.lazada_cod, 0) -
                                COALESCE(srbk.lazada_cod, 0)
                            )
                        ) / 1.011
                    ) AS BIGINT
                ) AS total_biaya_kirim_cod_dikurangi_ppn,
                CAST(
                    ROUND(
                        (
                            (
                                (
                                    COALESCE(sbk.shopee_cod, 0) -
                                    COALESCE(srbk.shopee_cod, 0)
                                ) +
                                (
                                    COALESCE(sbk.magellan_cod, 0) -
                                    COALESCE(srbk.magellan_cod, 0)
                                ) +
                                (
                                    COALESCE(sbk.lazada_cod, 0) -
                                    COALESCE(srbk.lazada_cod, 0)
                                )
                            ) / 1.011
                        ) * 0.07
                    ) AS BIGINT
                ) AS diskon_cod_7,
                COALESCE(sbk.tokopedia, 0) AS tokopedia,
                COALESCE(sbk.tokopedia, 0) AS total_biaya_kirim_tokopedia,
                CAST(
                    ROUND(
                        COALESCE(sbk.tokopedia, 0) / 1.011
                    ) AS BIGINT
                ) AS total_biaya_kirim_tokopedia_dikurangi_ppn,
                CAST(
                    ROUND(
                        (
                            COALESCE(sbk.tokopedia, 0) / 1.011
                        ) * 0.1
                    ) AS BIGINT
                ) AS discount_tokopedia_10,
                CAST(
                    (
                        ROUND(
                            (
                                (
                                    COALESCE(sbk.bukalapak, 0) +
                                    COALESCE(sbk.bukaexpress, 0) +
                                    COALESCE(sbk.bukasend, 0)
                                ) / 1.011
                            ) * 0.05
                        ) +
                        ROUND(
                            (
                                (
                                    (
                                        COALESCE(sbk.shopee_cod, 0) -
                                        COALESCE(srbk.shopee_cod, 0)
                                    ) +
                                    (
                                        COALESCE(sbk.magellan_cod, 0) -
                                        COALESCE(srbk.magellan_cod, 0)
                                    ) +
                                    (
                                        COALESCE(sbk.lazada_cod, 0) -
                                        COALESCE(srbk.lazada_cod, 0)
                                    )
                                ) / 1.011
                            ) * 0.07
                        ) +
                        ROUND(
                            (
                                COALESCE(sbk.tokopedia, 0) / 1.011
                            ) * 0.1
                        )
                    ) AS BIGINT
                ) AS cashback_marketplace
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_all_count_sum AS acs ON cp.drop_point_outgoing = acs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_reguler_count_sum AS rcs ON cp.drop_point_outgoing = rcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_dfod_count_sum AS dcs ON cp.drop_point_outgoing = dcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_super_count_sum AS scs ON cp.drop_point_outgoing = scs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".rekap_zonasi AS rzmp ON cp.drop_point_outgoing = rzmp.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'A'
        ";
    }

    public function createViewCPDPCashbackNonCODGrading1($schema) {
        return "
            CREATE OR REPLACE VIEW cp_dp_cashback_non_cod_grading_1 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                COALESCE(sbk.lazada, 0) AS lazada,
                COALESCE(srbk.lazada, 0) AS retur_lazada,
                COALESCE(sbk.shopee, 0) AS shopee,
                COALESCE(srbk.shopee, 0) AS retur_shopee,
                --tokotalk 0
                COALESCE(sbk.magellan, 0) AS magellan,
                COALESCE(srbk.magellan, 0) AS retur_magellan,
                (
                    COALESCE(srbk.akulakuob, 0) +
                    COALESCE(srbk.bukaexpress, 0) +
                    COALESCE(srbk.evermosapi, 0) +
                    COALESCE(srbk.mengantar, 0) +
                    COALESCE(srbk.ordivo, 0) +
                    COALESCE(srbk.tokopedia, 0)
                ) AS total_retur_pilihan,
                (
                    (COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0)) +
                    (COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0)) +
                    (COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0)) +
                    (
                        COALESCE(srbk.akulakuob, 0) +
                        COALESCE(srbk.bukaexpress, 0) +
                        COALESCE(srbk.evermosapi, 0) +
                        COALESCE(srbk.mengantar, 0) +
                        COALESCE(srbk.ordivo, 0) +
                        COALESCE(srbk.tokopedia, 0)
                    ) +
                    0 --retur belum terpotong
                ) AS total_biaya_kirim_non_cod,
                (
                CAST(ROUND((
                        (COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0)) +
                        (COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0)) +
                        (COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0)) +
                        (
                            COALESCE(srbk.akulakuob, 0) +
                            COALESCE(srbk.bukaexpress, 0) +
                            COALESCE(srbk.evermosapi, 0) +
                            COALESCE(srbk.mengantar, 0) +
                            COALESCE(srbk.ordivo, 0) +
                            COALESCE(srbk.tokopedia, 0)
                        ) +
                        0 --retur belum terpotong
                    ) / 1.011) AS BIGINT)) AS total_biaya_kirim_non_cod_dikurangi_ppn,
                    (
                CAST(
                    ROUND(
                    (
                            (COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0)) +
                            (COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0)) +
                            (COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0)) +
                            (
                                COALESCE(srbk.akulakuob, 0) +
                                COALESCE(srbk.bukaexpress, 0) +
                                COALESCE(srbk.evermosapi, 0) +
                                COALESCE(srbk.mengantar, 0) +
                                COALESCE(srbk.ordivo, 0) +
                                COALESCE(srbk.tokopedia, 0)
                            ) +
                            0 --retur belum terpotong
                        ) / 1.011
                    ) * 0.09 AS BIGINT)) AS discount_total_biaya_kirim_9,
                    (	CAST(
                        (
                            ROUND(
                                (
                                    (
                                        COALESCE(sbk.bukalapak, 0) +
                                        COALESCE(sbk.bukaexpress, 0) +
                                        COALESCE(sbk.bukasend, 0)
                                    ) / 1.011
                                ) * 0.05
                            ) +
                            ROUND(
                                (
                                    (
                                        (
                                            COALESCE(sbk.shopee_cod, 0) -
                                            COALESCE(srbk.shopee_cod, 0)
                                        ) +
                                        (
                                            COALESCE(sbk.magellan_cod, 0) -
                                            COALESCE(srbk.magellan_cod, 0)
                                        ) +
                                        (
                                            COALESCE(sbk.lazada_cod, 0) -
                                            COALESCE(srbk.lazada_cod, 0)
                                        )
                                    ) / 1.011
                                ) * 0.07
                            ) +
                            ROUND(
                                (
                                    COALESCE(sbk.tokopedia, 0) / 1.011
                                ) * 0.1
                            )
                        ) AS BIGINT
                    ) +
                    CAST(
                        ROUND(
                        (
                                (COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0)) +
                                (COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0)) +
                                (COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0)) +
                                (
                                    COALESCE(srbk.akulakuob, 0) +
                                    COALESCE(srbk.bukaexpress, 0) +
                                    COALESCE(srbk.evermosapi, 0) +
                                    COALESCE(srbk.mengantar, 0) +
                                    COALESCE(srbk.ordivo, 0) +
                                    COALESCE(srbk.tokopedia, 0)
                                ) +
                                0 --retur belum terpotong
                            ) / 1.011
                        ) * 0.09 AS BIGINT)
                ) AS total_cashback_marketplace
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_all_count_sum AS acs ON cp.drop_point_outgoing = acs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_reguler_count_sum AS rcs ON cp.drop_point_outgoing = rcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_dfod_count_sum AS dcs ON cp.drop_point_outgoing = dcs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_super_count_sum AS scs ON cp.drop_point_outgoing = scs.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".rekap_zonasi AS rzmp ON cp.drop_point_outgoing = rzmp.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'A'

        ";
    }

    public function createViewCPDPCashbackRekapGrading1($schema) {
        return "
            CREATE OR REPLACE VIEW cp_dp_rekap_cashback_grading_1 AS
            SELECT
                cp.kode_cp,
            cp.nama_cp,
            cpdpcrg.total_cashback_reguler AS total_cashback_reguler,
            cpdpcncg.total_cashback_marketplace AS total_cashback_marketplace,
            rzmp.total_cashback_marketplace AS total_cashback_mp_luar_zona,
            COALESCE(cpdpcrg.total_cashback_reguler, 0) + COALESCE(cpdpcncg.total_cashback_marketplace,0) + COALESCE(rzmp.total_cashback_marketplace, 0) AS total_cashback
            FROM
                PUBLIC.master_collection_point cp
            LEFT JOIN ".$schema.".rekap_zonasi rzmp ON cp.drop_point_outgoing = rzmp.drop_point_outgoing
            LEFT JOIN ".$schema.".cp_dp_cashback_reguler_grading_1 cpdpcrg ON cp.drop_point_outgoing = cpdpcrg.nama_cp
            LEFT JOIN ".$schema.".cp_dp_cashback_non_cod_grading_1 cpdpcncg ON cp.drop_point_outgoing = cpdpcncg.nama_cp
            WHERE
                cp.grading_pickup = 'A'
        ";
    }

    public function createViewCPDPRekapDendaGrading1($schema){
        return "
            CREATE OR REPLACE VIEW cp_dp_rekap_denda_cashback_grading_1 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                cp.nama_pt,
                cpdprcg.total_cashback,
                COALESCE(dg.transit_fee , 0) AS transit_fee,
                cpdprcg.total_cashback - COALESCE(dg.transit_fee , 0) AS total_cashback_dikurangi_transit_fee,
                COALESCE(dg.denda_void , 0) AS denda_void,
                COALESCE(dg.denda_dfod , 0) AS denda_dfod,
                COALESCE(dg.denda_pusat , 0) AS denda_pusat,
                COALESCE(dg.denda_selisih_berat , 0) AS denda_selisih_berat,
                COALESCE(dg.denda_lost_scan_kirim , 0) AS denda_lost_scan_kirim,
                COALESCE(dg.denda_auto_claim , 0) AS denda_auto_claim,
                COALESCE(dg.denda_sponsorship , 0) AS denda_sponsorship,
                COALESCE(dg.denda_late_pickup_ecommerce , 0) AS denda_late_pickup_ecommerce,
                COALESCE(dg.potongan_pop, 0) AS potongan_pop,
                COALESCE(dg.denda_lainnya, 0) AS denda_lainnya,
                COALESCE(dg.total_denda, 0) AS total_denda,
                ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011)  AS dpp,
                ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02) AS amount_pph_2,
                ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011) - ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02) AS amount_setelah_pph,
                CASE
                    WHEN cp.nama_bank <> 'BCA' THEN 2900
                    ELSE 0
                END AS admin_bank,
                CASE
                    WHEN cp.nama_bank <> 'BCA' THEN
                        (ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011) - ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02) - 2900)
                    ELSE
                    ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011) - ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02)
                END AS amount_setelah_potongan,
                cp.nama_bank
            FROM
                ".$schema.".cp_dp_rekap_cashback_grading_1 AS cpdprcg
            LEFT JOIN
                PUBLIC.master_collection_point AS cp ON cp.drop_point_outgoing = cpdprcg.nama_cp
            LEFT JOIN
                (
                    SELECT
                    *,
                    -- Calculate total denda by summing up all denda columns
                    (
                            transit_fee + denda_void + denda_dfod + denda_pusat + denda_selisih_berat
                        + denda_lost_scan_kirim + denda_auto_claim + denda_sponsorship
                        + denda_late_pickup_ecommerce + potongan_pop + denda_lainnya
                        ) AS total_denda
                FROM
                    denda_grading_periode
                ) AS dg ON dg.sprinter_pickup = cp.id
            WHERE
                cp.grading_pickup = 'A'
            ORDER BY cp.nama_pt
        ";
    }

    public function createViewCPDPCashbackAWBGrading2($schema){
        return "
        CREATE OR REPLACE VIEW cp_dp_cashback_awb_grading_2 AS
        SELECT
            cp.kode_cp,
            cp.nama_cp,
            (
                 COALESCE(mpcw.bukalapak, 0) +
                 COALESCE(mpcw.bukaexpress, 0) +
                 COALESCE(mpcw.bukasend, 0)
            ) AS awb_all_bukalapak,
            (
                 COALESCE(mpcw.shopee, 0) +
                 COALESCE(mpcw.shopee_cod, 0)
            ) AS awb_all_shopee,
            (
                 COALESCE(mpcw.lazada, 0) +
                 COALESCE(mpcw.lazada_cod, 0)
            ) AS awb_all_lazada,
            COALESCE(mpcw.tokopedia, 0) AS awb_tokopedia,
            --COALESCE(mpcw.tokotalk, 0) AS awb_tokotalk,
            (
                 COALESCE(mpcw.magellan, 0) +
                 COALESCE(mpcw.magellan_cod, 0)
            ) AS awb_all_magellan,
            COALESCE(mpcw.akulakuob, 0) AS awb_akulakuob,
            COALESCE(mpcw.ordivo, 0) AS awb_ordivo,
            COALESCE(mpcw.akulakuob, 0) AS awb_evermosapi,
            (
                 COALESCE(mprcw.shopee, 0) +
                 COALESCE(mprcw.shopee_cod, 0)
            ) AS awb_retur_all_shopee,
            (
                 COALESCE(mprcw.magellan, 0) +
                 COALESCE(mprcw.magellan_cod, 0)
            ) AS awb_retur_all_magellan,
            (
                COALESCE(mprcw.akulakuob, 0) +
                COALESCE(mprcw.bukaexpress, 0) +
                COALESCE(mprcw.evermosapi, 0) +
                COALESCE(mprcw.mengantar, 0) +
                COALESCE(mprcw.ordivo, 0) +
                COALESCE(mprcw.tokopedia, 0)
           ) AS total_retur_pilihan,
           --AS retur_belum_dipotong,
           (
                (
                    COALESCE(mpcw.bukalapak, 0) +
                  COALESCE(mpcw.bukaexpress, 0) +
                  COALESCE(mpcw.bukasend, 0) +
                  COALESCE(mpcw.shopee, 0) +
                  COALESCE(mpcw.shopee_cod, 0) +
                  COALESCE(mpcw.lazada, 0) +
                  COALESCE(mpcw.lazada_cod, 0) +
                  COALESCE(mpcw.tokopedia, 0) +
                  COALESCE(mpcw.magellan, 0) +
                  COALESCE(mpcw.magellan_cod, 0) +
                  COALESCE(mpcw.akulakuob, 0) +
                  COALESCE(mpcw.ordivo, 0) +
                  COALESCE(mpcw.akulakuob, 0)
                ) -
                (
                  COALESCE(mprcw.shopee, 0) +
                 COALESCE(mprcw.shopee_cod, 0) +
                 COALESCE(mprcw.magellan, 0) +
                 COALESCE(mprcw.magellan_cod, 0) +
                 COALESCE(mprcw.akulakuob, 0) +
                COALESCE(mprcw.bukaexpress, 0) +
                COALESCE(mprcw.evermosapi, 0) +
                COALESCE(mprcw.mengantar, 0) +
                COALESCE(mprcw.ordivo, 0) +
                COALESCE(mprcw.tokopedia, 0)
                )
            ) AS total_awb,
            (
                (
                    (
                        COALESCE(mpcw.bukalapak, 0) +
                      COALESCE(mpcw.bukaexpress, 0) +
                      COALESCE(mpcw.bukasend, 0) +
                      COALESCE(mpcw.shopee, 0) +
                      COALESCE(mpcw.shopee_cod, 0) +
                      COALESCE(mpcw.lazada, 0) +
                      COALESCE(mpcw.lazada_cod, 0) +
                      COALESCE(mpcw.tokopedia, 0) +
                      COALESCE(mpcw.magellan, 0) +
                      COALESCE(mpcw.magellan_cod, 0) +
                      COALESCE(mpcw.akulakuob, 0) +
                      COALESCE(mpcw.ordivo, 0) +
                      COALESCE(mpcw.akulakuob, 0)
                    ) -
                    (
                      COALESCE(mprcw.shopee, 0) +
                     COALESCE(mprcw.shopee_cod, 0) +
                     COALESCE(mprcw.magellan, 0) +
                     COALESCE(mprcw.magellan_cod, 0) +
                     COALESCE(mprcw.akulakuob, 0) +
                    COALESCE(mprcw.bukaexpress, 0) +
                    COALESCE(mprcw.evermosapi, 0) +
                    COALESCE(mprcw.mengantar, 0) +
                    COALESCE(mprcw.ordivo, 0) +
                    COALESCE(mprcw.tokopedia, 0)
                    )
                ) * 750
            ) AS discount_awb,
            CAST(
                ROUND
                (
                    (
                        (
                            COALESCE(mpcw.bukalapak, 0) +
                          COALESCE(mpcw.bukaexpress, 0) +
                          COALESCE(mpcw.bukasend, 0) +
                          COALESCE(mpcw.shopee, 0) +
                          COALESCE(mpcw.shopee_cod, 0) +
                          COALESCE(mpcw.lazada, 0) +
                          COALESCE(mpcw.lazada_cod, 0) +
                          COALESCE(mpcw.tokopedia, 0) +
                          COALESCE(mpcw.magellan, 0) +
                          COALESCE(mpcw.magellan_cod, 0) +
                          COALESCE(mpcw.akulakuob, 0) +
                          COALESCE(mpcw.ordivo, 0) +
                          COALESCE(mpcw.akulakuob, 0)
                        ) -
                        (
                          COALESCE(mprcw.shopee, 0) +
                         COALESCE(mprcw.shopee_cod, 0) +
                         COALESCE(mprcw.magellan, 0) +
                         COALESCE(mprcw.magellan_cod, 0) +
                         COALESCE(mprcw.akulakuob, 0) +
                        COALESCE(mprcw.bukaexpress, 0) +
                        COALESCE(mprcw.evermosapi, 0) +
                        COALESCE(mprcw.mengantar, 0) +
                        COALESCE(mprcw.ordivo, 0) +
                        COALESCE(mprcw.tokopedia, 0)
                        )
                    ) * 750 * 0.011 )
            AS BIGINT) AS ppn,
            CAST(
                ROUND
                (
                    (
                        (
                            COALESCE(mpcw.bukalapak, 0) +
                          COALESCE(mpcw.bukaexpress, 0) +
                          COALESCE(mpcw.bukasend, 0) +
                          COALESCE(mpcw.shopee, 0) +
                          COALESCE(mpcw.shopee_cod, 0) +
                          COALESCE(mpcw.lazada, 0) +
                          COALESCE(mpcw.lazada_cod, 0) +
                          COALESCE(mpcw.tokopedia, 0) +
                          COALESCE(mpcw.magellan, 0) +
                          COALESCE(mpcw.magellan_cod, 0) +
                          COALESCE(mpcw.akulakuob, 0) +
                          COALESCE(mpcw.ordivo, 0) +
                          COALESCE(mpcw.akulakuob, 0)
                        ) -
                        (
                          COALESCE(mprcw.shopee, 0) +
                         COALESCE(mprcw.shopee_cod, 0) +
                         COALESCE(mprcw.magellan, 0) +
                         COALESCE(mprcw.magellan_cod, 0) +
                         COALESCE(mprcw.akulakuob, 0) +
                        COALESCE(mprcw.bukaexpress, 0) +
                        COALESCE(mprcw.evermosapi, 0) +
                        COALESCE(mprcw.mengantar, 0) +
                        COALESCE(mprcw.ordivo, 0) +
                        COALESCE(mprcw.tokopedia, 0)
                        )
                    ) * 750 / 1.011 )
            AS BIGINT)AS total_cashback_marketplace
        FROM
            public.master_collection_point AS cp
        LEFT JOIN
           ".$schema.".cp_dp_mp_count_waybill AS mpcw ON cp.drop_point_outgoing = mpcw.drop_point_outgoing
        LEFT JOIN
           ".$schema.".cp_dp_mp_retur_count_waybill AS mprcw ON cp.drop_point_outgoing = mprcw.drop_point_outgoing
        WHERE
            cp.grading_pickup = 'B'
        ";
    }

    public function createViewCPDPCashbackRekapGrading2($schema){
        return "
            CREATE OR REPLACE VIEW cp_dp_rekap_cashback_grading_2 AS
            SELECT
            cp.kode_cp,
            cp.nama_cp,
            cpdpcrg.total_cashback_reguler AS total_cashback_reguler,
            cbawbcg.total_cashback_marketplace AS total_cashback_marketplace,
            COALESCE(cpdpcrg.total_cashback_reguler, 0) + COALESCE(cbawbcg.total_cashback_marketplace,0) AS total_cashback
            FROM
            PUBLIC.master_collection_point cp
            LEFT JOIN ".$schema.".cp_dp_cashback_reguler_grading_2 cpdpcrg ON cp.drop_point_outgoing = cpdpcrg.nama_cp
            LEFT JOIN ".$schema.".cp_dp_cashback_awb_grading_2 cbawbcg ON cp.drop_point_outgoing = cbawbcg.nama_cp
            WHERE
            cp.grading_pickup = 'B'
        ";
    }

    public function createViewCPDPCashbackRekapDendaGrading2($schema){
        return "
            CREATE OR REPLACE VIEW cp_dp_rekap_denda_cashback_grading_2 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                cp.nama_pt,
                cpdprcg.total_cashback,
                COALESCE(dg.transit_fee , 0) AS transit_fee,
                cpdprcg.total_cashback - COALESCE(dg.transit_fee , 0) AS total_cashback_dikurangi_transit_fee,
                COALESCE(dg.denda_void , 0) AS denda_void,
                COALESCE(dg.denda_dfod , 0) AS denda_dfod,
                COALESCE(dg.denda_pusat , 0) AS denda_pusat,
                COALESCE(dg.denda_selisih_berat , 0) AS denda_selisih_berat,
                COALESCE(dg.denda_lost_scan_kirim , 0) AS denda_lost_scan_kirim,
                COALESCE(dg.denda_auto_claim , 0) AS denda_auto_claim,
                COALESCE(dg.denda_sponsorship , 0) AS denda_sponsorship,
                COALESCE(dg.denda_late_pickup_ecommerce , 0) AS denda_late_pickup_ecommerce,
                COALESCE(dg.potongan_pop, 0) AS potongan_pop,
                COALESCE(dg.denda_lainnya, 0) AS denda_lainnya,
                COALESCE(dg.total_denda, 0) AS total_denda,
                ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011)  AS dpp,
                ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02) AS amount_pph_2,
                ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011) - ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02) AS amount_setelah_pph,
                CASE
                    WHEN cp.nama_bank <> 'BCA' THEN 2900
                    ELSE 0
                END AS admin_bank,
                CASE
                    WHEN cp.nama_bank <> 'BCA' THEN
                        (ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011) - ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02) - 2900)
                    ELSE
                    ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011) - ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02)
                END AS amount_setelah_potongan,
                cp.nama_bank
            FROM
                ".$schema.".cp_dp_rekap_cashback_grading_2 AS cpdprcg
            LEFT JOIN
                PUBLIC.master_collection_point AS cp ON cp.drop_point_outgoing = cpdprcg.nama_cp
            LEFT JOIN
                (
                    SELECT
                    *,
                    -- Calculate total denda by summing up all denda columns
                    (
                            transit_fee + denda_void + denda_dfod + denda_pusat + denda_selisih_berat
                        + denda_lost_scan_kirim + denda_auto_claim + denda_sponsorship
                        + denda_late_pickup_ecommerce + potongan_pop + denda_lainnya
                        ) AS total_denda
                FROM
                    denda_grading_periode
                ) AS dg ON dg.sprinter_pickup = cp.id
            WHERE
                cp.grading_pickup = 'B'
            ORDER BY cp.nama_pt
        ";
    }

    public function createViewCPDPCashbackAWBGrading3($schema){
        return "
            CREATE OR REPLACE VIEW cp_dp_cashback_awb_grading_3 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                (
                    COALESCE(mpcw.bukalapak, 0) +
                    COALESCE(mpcw.bukaexpress, 0) +
                    COALESCE(mpcw.bukasend, 0)
                ) AS awb_all_bukalapak,
                (
                    COALESCE(mpcw.shopee, 0) +
                    COALESCE(mpcw.shopee_cod, 0)
                ) AS awb_all_shopee,
                (
                    COALESCE(mpcw.lazada, 0) +
                    COALESCE(mpcw.lazada_cod, 0)
                ) AS awb_all_lazada,
                COALESCE(mpcw.tokopedia, 0) AS awb_tokopedia,
                --COALESCE(mpcw.tokotalk, 0) AS awb_tokotalk,
                (
                    COALESCE(mpcw.magellan, 0) +
                    COALESCE(mpcw.magellan_cod, 0)
                ) AS awb_all_magellan,
                COALESCE(mpcw.akulakuob, 0) AS awb_akulakuob,
                COALESCE(mpcw.ordivo, 0) AS awb_ordivo,
                COALESCE(mpcw.akulakuob, 0) AS awb_evermosapi,
                (
                    COALESCE(mprcw.shopee, 0) +
                    COALESCE(mprcw.shopee_cod, 0)
                ) AS awb_retur_all_shopee,
                (
                    COALESCE(mprcw.magellan, 0) +
                    COALESCE(mprcw.magellan_cod, 0)
                ) AS awb_retur_all_magellan,
                (
                    COALESCE(mprcw.akulakuob, 0) +
                    COALESCE(mprcw.bukaexpress, 0) +
                    COALESCE(mprcw.evermosapi, 0) +
                    COALESCE(mprcw.mengantar, 0) +
                    COALESCE(mprcw.ordivo, 0) +
                    COALESCE(mprcw.tokopedia, 0)
            ) AS total_retur_pilihan,
            --AS retur_belum_dipotong,
            (
                    (
                        COALESCE(mpcw.bukalapak, 0) +
                    COALESCE(mpcw.bukaexpress, 0) +
                    COALESCE(mpcw.bukasend, 0) +
                    COALESCE(mpcw.shopee, 0) +
                    COALESCE(mpcw.shopee_cod, 0) +
                    COALESCE(mpcw.lazada, 0) +
                    COALESCE(mpcw.lazada_cod, 0) +
                    COALESCE(mpcw.tokopedia, 0) +
                    COALESCE(mpcw.magellan, 0) +
                    COALESCE(mpcw.magellan_cod, 0) +
                    COALESCE(mpcw.akulakuob, 0) +
                    COALESCE(mpcw.ordivo, 0) +
                    COALESCE(mpcw.akulakuob, 0)
                    ) -
                    (
                    COALESCE(mprcw.shopee, 0) +
                    COALESCE(mprcw.shopee_cod, 0) +
                    COALESCE(mprcw.magellan, 0) +
                    COALESCE(mprcw.magellan_cod, 0) +
                    COALESCE(mprcw.akulakuob, 0) +
                    COALESCE(mprcw.bukaexpress, 0) +
                    COALESCE(mprcw.evermosapi, 0) +
                    COALESCE(mprcw.mengantar, 0) +
                    COALESCE(mprcw.ordivo, 0) +
                    COALESCE(mprcw.tokopedia, 0)
                    )
                ) AS total_awb,
                (
                    (
                        (
                            COALESCE(mpcw.bukalapak, 0) +
                        COALESCE(mpcw.bukaexpress, 0) +
                        COALESCE(mpcw.bukasend, 0) +
                        COALESCE(mpcw.shopee, 0) +
                        COALESCE(mpcw.shopee_cod, 0) +
                        COALESCE(mpcw.lazada, 0) +
                        COALESCE(mpcw.lazada_cod, 0) +
                        COALESCE(mpcw.tokopedia, 0) +
                        COALESCE(mpcw.magellan, 0) +
                        COALESCE(mpcw.magellan_cod, 0) +
                        COALESCE(mpcw.akulakuob, 0) +
                        COALESCE(mpcw.ordivo, 0) +
                        COALESCE(mpcw.akulakuob, 0)
                        ) -
                        (
                        COALESCE(mprcw.shopee, 0) +
                        COALESCE(mprcw.shopee_cod, 0) +
                        COALESCE(mprcw.magellan, 0) +
                        COALESCE(mprcw.magellan_cod, 0) +
                        COALESCE(mprcw.akulakuob, 0) +
                        COALESCE(mprcw.bukaexpress, 0) +
                        COALESCE(mprcw.evermosapi, 0) +
                        COALESCE(mprcw.mengantar, 0) +
                        COALESCE(mprcw.ordivo, 0) +
                        COALESCE(mprcw.tokopedia, 0)
                        )
                    ) * 750
                ) AS discount_awb,
                CAST(
                    ROUND
                    (
                        (
                            (
                                COALESCE(mpcw.bukalapak, 0) +
                            COALESCE(mpcw.bukaexpress, 0) +
                            COALESCE(mpcw.bukasend, 0) +
                            COALESCE(mpcw.shopee, 0) +
                            COALESCE(mpcw.shopee_cod, 0) +
                            COALESCE(mpcw.lazada, 0) +
                            COALESCE(mpcw.lazada_cod, 0) +
                            COALESCE(mpcw.tokopedia, 0) +
                            COALESCE(mpcw.magellan, 0) +
                            COALESCE(mpcw.magellan_cod, 0) +
                            COALESCE(mpcw.akulakuob, 0) +
                            COALESCE(mpcw.ordivo, 0) +
                            COALESCE(mpcw.akulakuob, 0)
                            ) -
                            (
                            COALESCE(mprcw.shopee, 0) +
                            COALESCE(mprcw.shopee_cod, 0) +
                            COALESCE(mprcw.magellan, 0) +
                            COALESCE(mprcw.magellan_cod, 0) +
                            COALESCE(mprcw.akulakuob, 0) +
                            COALESCE(mprcw.bukaexpress, 0) +
                            COALESCE(mprcw.evermosapi, 0) +
                            COALESCE(mprcw.mengantar, 0) +
                            COALESCE(mprcw.ordivo, 0) +
                            COALESCE(mprcw.tokopedia, 0)
                            )
                        ) * 750 * 0.011 )
                AS BIGINT) AS ppn,
                CAST(
                    ROUND
                    (
                        (
                            (
                                COALESCE(mpcw.bukalapak, 0) +
                            COALESCE(mpcw.bukaexpress, 0) +
                            COALESCE(mpcw.bukasend, 0) +
                            COALESCE(mpcw.shopee, 0) +
                            COALESCE(mpcw.shopee_cod, 0) +
                            COALESCE(mpcw.lazada, 0) +
                            COALESCE(mpcw.lazada_cod, 0) +
                            COALESCE(mpcw.tokopedia, 0) +
                            COALESCE(mpcw.magellan, 0) +
                            COALESCE(mpcw.magellan_cod, 0) +
                            COALESCE(mpcw.akulakuob, 0) +
                            COALESCE(mpcw.ordivo, 0) +
                            COALESCE(mpcw.akulakuob, 0)
                            ) -
                            (
                            COALESCE(mprcw.shopee, 0) +
                            COALESCE(mprcw.shopee_cod, 0) +
                            COALESCE(mprcw.magellan, 0) +
                            COALESCE(mprcw.magellan_cod, 0) +
                            COALESCE(mprcw.akulakuob, 0) +
                            COALESCE(mprcw.bukaexpress, 0) +
                            COALESCE(mprcw.evermosapi, 0) +
                            COALESCE(mprcw.mengantar, 0) +
                            COALESCE(mprcw.ordivo, 0) +
                            COALESCE(mprcw.tokopedia, 0)
                            )
                        ) * 750 / 1.011 )
                AS BIGINT)AS total_cashback_marketplace
            FROM
                public.master_collection_point AS cp
            LEFT JOIN
            ".$schema.".cp_dp_mp_count_waybill AS mpcw ON cp.drop_point_outgoing = mpcw.drop_point_outgoing
            LEFT JOIN
            ".$schema.".cp_dp_mp_retur_count_waybill AS mprcw ON cp.drop_point_outgoing = mprcw.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'C'
        ";
    }

    public function createViewCPDPCashbackRekapGrading3($schema){
        return "
            CREATE OR REPLACE VIEW cp_dp_rekap_cashback_grading_3 AS
            SELECT
                cp.kode_cp,
            cp.nama_cp,
            cpdpcrg.total_cashback_reguler AS total_cashback_reguler,
            cbawbcg.total_cashback_marketplace AS total_cashback_marketplace,
            COALESCE(cpdpcrg.total_cashback_reguler, 0) + COALESCE(cbawbcg.total_cashback_marketplace,0) AS total_cashback
            FROM
                PUBLIC.master_collection_point cp
            LEFT JOIN ".$schema.".cp_dp_cashback_reguler_grading_3 cpdpcrg ON cp.drop_point_outgoing = cpdpcrg.nama_cp
            LEFT JOIN ".$schema.".cp_dp_cashback_awb_grading_3 cbawbcg ON cp.drop_point_outgoing = cbawbcg.nama_cp
            WHERE
                cp.grading_pickup = 'C'
        ";
    }

    public function createViewCPDPCashbackRekapDendaGrading3($schema){
        return "
            CREATE OR REPLACE VIEW cp_dp_rekap_denda_cashback_grading_3 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                cp.nama_pt,
                cpdprcg.total_cashback,
                COALESCE(dg.transit_fee , 0) AS transit_fee,
                cpdprcg.total_cashback - COALESCE(dg.transit_fee , 0) AS total_cashback_dikurangi_transit_fee,
                COALESCE(dg.denda_void , 0) AS denda_void,
                COALESCE(dg.denda_dfod , 0) AS denda_dfod,
                COALESCE(dg.denda_pusat , 0) AS denda_pusat,
                COALESCE(dg.denda_selisih_berat , 0) AS denda_selisih_berat,
                COALESCE(dg.denda_lost_scan_kirim , 0) AS denda_lost_scan_kirim,
                COALESCE(dg.denda_auto_claim , 0) AS denda_auto_claim,
                COALESCE(dg.denda_sponsorship , 0) AS denda_sponsorship,
                COALESCE(dg.denda_late_pickup_ecommerce , 0) AS denda_late_pickup_ecommerce,
                COALESCE(dg.potongan_pop, 0) AS potongan_pop,
                COALESCE(dg.denda_lainnya, 0) AS denda_lainnya,
                COALESCE(dg.total_denda, 0) AS total_denda,
                ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011)  AS dpp,
                ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02) AS amount_pph_2,
                ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011) - ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02) AS amount_setelah_pph,
                CASE
                    WHEN cp.nama_bank <> 'BCA' THEN 2900
                    ELSE 0
                END AS admin_bank,
                CASE
                    WHEN cp.nama_bank <> 'BCA' THEN
                        (ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011) - ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02) - 2900)
                    ELSE
                    ROUND((cpdprcg.total_cashback - dg.total_denda) / 1.011) - ROUND(((cpdprcg.total_cashback - dg.total_denda) / 1.011) * 0.02)
                END AS amount_setelah_potongan,
                cp.nama_bank
            FROM
                ".$schema.".cp_dp_rekap_cashback_grading_3 AS cpdprcg
            LEFT JOIN
                PUBLIC.master_collection_point AS cp ON cp.drop_point_outgoing = cpdprcg.nama_cp
            LEFT JOIN
                (
                    SELECT
                    *,
                    -- Calculate total denda by summing up all denda columns
                    (
                            transit_fee + denda_void + denda_dfod + denda_pusat + denda_selisih_berat
                        + denda_lost_scan_kirim + denda_auto_claim + denda_sponsorship
                        + denda_late_pickup_ecommerce + potongan_pop + denda_lainnya
                        ) AS total_denda
                FROM
                    denda_grading_periode
                ) AS dg ON dg.sprinter_pickup = cp.id
            WHERE
                cp.grading_pickup = 'C'
            ORDER BY cp.nama_pt
        ";
    }
}
