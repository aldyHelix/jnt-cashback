<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class CashbackGrading3Export implements FromCollection
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
            $output[] = array_keys($array[0]);
            // store values for each row
            foreach ($array as $row) {
                $output[] = array_values($row);
            }
            // add an empty row before the next dataset
            $output[] = [''];
        }

        $this->collection = collect($output);
    }

    public function collection()
    {
        return $this->collection;
    }
}