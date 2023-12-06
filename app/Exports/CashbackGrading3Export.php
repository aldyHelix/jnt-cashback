<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CashbackGrading3Export implements FromCollection, ShouldAutoSize, WithHeadings
{
    private $collection;
    private $fileName;

    public function __construct($arrays, $fileName)
    {
        $this->fileName = $fileName;
        $output = [];

        foreach ($arrays as $array) {
            foreach($array as $key => $item) {
                $array[$key] = get_object_vars($item);
            }
            // get headers for current dataset
            //$output[] = array_keys($array[0]);
            // store values for each row
            foreach ($array as $row) {
                $output[] = array_values($row);
            }
            // add an empty row before the next dataset
            $output[] = [''];
        }

        $this->collection = collect($output);
    }

    public function headings(): array
    {
        return [
           ['' ,'PT ORIENTAL JAYA MANDIRI INDAH ( J&T EXPRESS )'],
           ['' , 'DATA DISCOUNT CUSTOMER CP'],
        ];
    }

    public function collection()
    {
        return $this->collection;
    }
}
