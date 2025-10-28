<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class YourImportClass implements ToArray, WithCalculatedFormulas
{
    /**
     * Handle the array data.
     *
     * @param array $array
     */
    public function array(array $array)
    {
        // This method will be called with the data from the Excel sheet
        // $array will contain calculated values instead of formulas

        // You can process the array as needed here
    }
}
