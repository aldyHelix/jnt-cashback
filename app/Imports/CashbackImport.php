<?php

namespace App\Imports;

use App\Models\Cashback;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Ramsey\Collection\Collection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CashbackImport implements ToModel, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return Cashback|null //21row
     */
    public function model(array $row)
    {
        return new Collection([
           'no_waybill'     => $row[0],
           'tanggal_pengiriman' => $row[1],
           'drop_point_outgoing' => $row[2],
           'sprinter_pickup' => $row[3],
           'tujuan' => $row[4],
           'berat_ditagih' => $row[5],
           'biaya_cod' => $row[6],
           'biaya_asuransi' => $row[7],
           'biaya_kirim' => $row[8],
           'biaya_lainnya' => $row[9],
           'total_biaya' => $row[10],
           'klien_pengirim' => $row[11],
           'cara_pembayaran' => $row[12],
           'nama_pengirim' => $row[13],
           'alamat_pengirim' => $row[14],
           'sumber_waybill' => $row[15],
           'retur' => $row[16],
           'waktu_ttd' => $row[17],
           'nominal_diskon' => $row[18],
           'biaya_setelah_diskon' => $row[19],
           'zona' => $row[20]
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
