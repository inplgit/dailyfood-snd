<?php

namespace App\Imports;

use App\Models\ProductPrice;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ProductPriceImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['product_id']) || !isset($row['uom_id'])) {
                continue;
            }

            ProductPrice::updateOrCreate(
                [
                    'product_id' => $row['product_id'],
                    'uom_id'     => $row['uom_id'],
                ],
                [
                    'retail_price'   => $row['retail_price'],
                    'trade_price'    => $row['trade_price'],
                    'pcs_per_carton' => $row['pcs_per_carton'],
                    'start_date'     => $row['start_date'] ?? date('Y-m-d'),
                ]
            );
        }
    }
}
