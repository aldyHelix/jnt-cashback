<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SumberWaybillSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sumberWaybillData = [
            [
                "table" => "data_mart",
                "rows" => [
                    ["AKULAKUOB"],
                    ["APP"],
                    ["APP Sprinter"],
                    ["BITESHIP"],
                    ["BLIBLIAPI"],
                    ["BRTTRIMENTARI"],
                    ["BUKAEXPRESS"],
                    ["BUKALAPAK"],
                    ["BUKASEND"],
                    ["CLODEOHQ"],
                    ["DESTYAPI"],
                    ["DOCTORSHIP"],
                    ["DONATELLOINDO"],
                    ["E3"],
                    ["EVERMOSAPI"],
                    ["GOAPOTIK"],
                    ["GRAMEDIA"],
                    ["JENIBOT"],
                    ["LAZADA"],
                    ["LAZADA COD"],
                    ["MAGELLAN"],
                    ["MAGELLAN COD"],
                    ["MAULAGI"],
                    ["MENGANTAR"],
                    ["MINISO"],
                    ["ORDIVO"],
                    ["PARAMA"],
                    ["PLUGO"],
                    ["RETURNKEY"],
                    ["SHIPPERID"],
                    ["SHOPEE"],
                    ["SHOPEE COD"],
                    ["SHOPEECB"],
                    ["SIRCLOSTORE"],
                    ["TOKOPEDIA"],
                    ["TRIES"],
                    ["VIP"],
                    ["VIP-APP"],
                    ["WEBSITE"],
                    ["WINGS"]
                ]
            ]
        ];

        foreach ($sumberWaybillData as $sumberWaybill) {
            foreach ($sumberWaybill["rows"] as $row) {
                DB::table('sumber_waybill_setting')->insert([
                    'sumber_waybill' => $row[0],
                    'type' => 'reguler',
                    'order' => 1,
                    'header_name' => $row[0],
                    'is_count' => 1,
                    'is_sum' => 0,
                    'is_active' => 1,
                ]);
            }
        }
    }
}
