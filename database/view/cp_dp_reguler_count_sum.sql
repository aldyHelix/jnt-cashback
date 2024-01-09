-- cashback_jan_2022.cp_dp_reguler_count_sum source

CREATE OR REPLACE VIEW cashback_jan_2022.cp_dp_reguler_count_sum AS
SELECT DISTINCT data_mart.drop_point_outgoing,
    count(data_mart.no_waybill) AS count,
    sum(data_mart.biaya_kirim) AS sum,
    sum(data_mart.total_biaya_setelah_diskon) AS sum_setelah_diskon
FROM cashback_jan_2022.data_mart
WHERE (data_mart.zona = 'CP'::text
    OR data_mart.zona = 'DP'::text
    OR data_mart.drop_point_outgoing::text = 'CP_BNR'::text AND data_mart.zona = 'DALAM ZONASI'::text
    OR data_mart.drop_point_outgoing::text = 'PESONA_DARUSSALAM'::text AND data_mart.zona = 'DALAM ZONASI'::text
    OR data_mart.drop_point_outgoing::text = 'PAMOYANAN_BOGOR'::text AND data_mart.zona = 'DALAM ZONASI'::text) AND (data_mart.metode_pembayaran = ''::text
    OR data_mart.metode_pembayaran = 'PP_CASH'::text
    OR data_mart.metode_pembayaran = 'PP_PM'::text) AND (data_mart.sumber_waybill = 'reguler'::text)
GROUP BY data_mart.drop_point_outgoing;
