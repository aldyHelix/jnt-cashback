<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GradingExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;
    protected $fileName;

    public function __construct($data, $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Kode CP',
            'Nama CP',
            'Biaya Kirim All',
            'Biaya Kirim Reguler',
            'Biaya Kirim DFOD',
            'Biaya Kirim Super',
            'Total Biaya Kirim',
            'Total Biaya Kirim Dikurangi PPN',
            'Amount Discount 25',
            'Akulaku',
            'Ordivo',
            'Evermos',
            'Mengantar',
            'Total Biaya Kirim A',
            'Total Biaya Kirim A Dikurangi PPN',
            'Amount Discount 10',
            'Total Cashback Reguler',
        ];
    }

    public function map($row): array
    {
        return [
            $row->kode_cp,
            $row->nama_cp,
            $row->biaya_kirim_all ?? 0,
            $row->biaya_kirim_reguler ?? 0,
            $row->biaya_kirim_dfod ?? 0,
            $row->biaya_kirim_super ?? 0,
            $row->total_biaya_kirim ?? 0,
            $row->total_biaya_kirim_dikurangi_ppn ?? 0,
            $row->amount_discount_25 ?? 0,
            $row->akulaku ?? 0,
            $row->ordivo ?? 0,
            $row->evermos ?? 0,
            $row->mengantar ?? 0,
            $row->total_biaya_kirim_a ?? 0,
            $row->total_biaya_kirim_a_dikurangi_ppn ?? 0,
            $row->amount_discount_10 ?? 0,
            $row->total_cashback_reguler ?? 0,
        ];
    }
}
