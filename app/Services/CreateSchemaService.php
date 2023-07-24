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
            )");

        return $created;
    }

    public function MPDeliveryAWB($schema) {
        return "
            CREATE OR REPLACE VIEW mp_delivery_count_sprinter AS
            SELECT DISTINCT data_mart.sprinter, COUNT(data_mart.no_waybill)
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

            ");
            // CREATE VIEW winners AS
            //     SELECT title, release FROM films WHERE awards IS NOT NULL;
        return $created;
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
}
