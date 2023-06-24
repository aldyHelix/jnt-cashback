<?php

namespace App\Imports;

use App\Models\Cashback;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class CashbackImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new Cashback([
           'resi'     => $row[0],
        ]);
    }
}
