<?php
namespace App\Services;

class GenerateDeliveryService {
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

    public function DeliveryPivot($schema) {
        if(Schema::hasTable($schema.'.data_mart')) {
            $run = DB::connection('pgsql')->unprepared(
                "
                    SET search_path TO ".$schema.",  public; \n

                    CREATE OR REPLACE VIEW ttd_list AS
                    SELECT DISTINCT data_mart.drop_point_ttd
                    FROM ".$schema.".data_mart;

                    CREATE OR REPLACE VIEW total_awb_by_ttd AS
                    SELECT drop_point_ttd, COUNT(no_waybill)
                    FROM ".$schema.".data_mart
                    GROUP BY data_mart.drop_point_ttd;

                    CREATE OR REPLACE VIEW direct_fee AS
                    SELECT data_mart.drop_point_outgoing, COUNT(no_waybill)
                    FROM ".$schema.".data_mart
                    GROUP BY data_mart.drop_point_outgoing;

                    CREATE OR REPLACE VIEW delivery_fee_summary AS
                    SELECT
                        main_query.*,
                        main_query.fee_setelah_kpi * main_query.total_awb AS total_delivery_fee,
                        CAST(ROUND((main_query.fee_setelah_kpi * main_query.total_awb) / 1.011, 2) AS BIGINT) AS total_delivery_setelah_ppn
                    FROM (
                        SELECT
                            mcp.zona_delivery AS kode_dp,
                            mcp.drop_point_outgoing AS drop_point,
                            deliv.drop_point_ttd AS dpf,
                            mdf.tarif AS tarif_per_awb,
                            deliv.count AS total_awb,
                            dz.kpi_target_count,
                            CASE
                                WHEN dz.kpi_target_count = 0 THEN 0
                                ELSE CAST(
                                    ROUND((deliv.count / dz.kpi_target_count) * 100, 2) AS DECIMAL(10, 2)
                                )
                            END AS kpi_delivery_percentage,
                            CASE
                                WHEN (deliv.count / dz.kpi_target_count) * 100 < 92 THEN mdf.tarif - dz.kpi_reduce_not_achievement
                                ELSE mdf.tarif
                            END AS fee_setelah_kpi
                        FROM ".$schema.".total_awb_by_ttd deliv
                        JOIN
                            PUBLIC.delivery_zone dz ON dz.drop_point_ttd = deliv.drop_point_ttd
                        JOIN
                            PUBLIC.master_collection_point mcp ON mcp.id = dz.collection_point_id
                        JOIN
                            PUBLIC.master_delivery_fee mdf ON mdf.zona = mcp.zona_delivery
                    ) AS main_query;

                    CREATE OR REPLACE VIEW rekap_denda_delivery_fee_summary AS
                    SELECT
                        main_query.*,
                        ROUND(CAST(main_query.delivery_fee AS DECIMAL(10,2)) * ROUND(CAST(CAST(main_query.tarif AS DECIMAL(10,2)) / 100 AS DECIMAL(10,2)) , 2)) AS pph,
                        main_query.delivery_fee - ROUND(CAST(main_query.delivery_fee AS DECIMAL(10,2)) * ROUND(CAST(CAST(main_query.tarif AS DECIMAL(10,2)) / 100 AS DECIMAL(10,2)) , 2)) AS amount_setelah_pph
                    FROM (
                        SELECT
                        deliv.kode_dp,
                        deliv.drop_point AS mitra,
                        deliv.total_delivery_setelah_ppn AS delivery_fee,
                        ddp.denda_lost_scan_kirim AS denda_lost_scan_kirim,
                        ddp.denda_late_pickup_reg AS denda_late_pickup_reg,
                        ddp.denda_auto_claim AS denda_auto_claim,
                        ddp.dpp AS dpp,
                        ddp.tarif AS tarif,
                        ddp.admin_bank AS admin_bank
                        FROM ".$schema.".delivery_fee_summary deliv
                        JOIN
                            PUBLIC.delivery_zone dz ON dz.drop_point_ttd = deliv.dpf
                        JOIN
                            PUBLIC.denda_delivery_periode ddp ON ddp.drop_point_outgoing = deliv.drop_point
                        JOIN
                            PUBLIC.master_collection_point mcp ON mcp.id = dz.collection_point_id
                        JOIN
                            PUBLIC.master_delivery_fee mdf ON mdf.zona = mcp.zona_delivery
                    ) AS main_query;

                ");

            return $run;
        }
    }

    public function createPivotPerTTD($schema, $ttd_name) {
        if(Schema::hasTable($schema.'.data_mart')) {
            $run = DB::connection('pgsql')->unprepared(
                "
                    SET search_path TO ".$schema.",  public; \n

                    CREATE OR REPLACE VIEW mp_".strtolower($ttd_name)." AS
                    SELECT sprinter, COUNT(no_waybill)
                    FROM ".$schema.".data_mart
                    WHERE drop_point_ttd = '".$ttd_name."'
                    GROUP BY sprinter;
                ");

            return $run;
        }
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
}
