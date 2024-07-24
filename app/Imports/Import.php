<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Import implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function __construct($headingRow = 1)
    {
        $this->headingRowInt = $headingRow;
    }

    public function collection(Collection $collection)
    {
        //
    }

    public function headingRow(): int
    {
        return $this->headingRowInt;
    }
}
