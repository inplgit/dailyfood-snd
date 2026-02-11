<?php

namespace App\Exports;

use App\Models\ProductPrice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductPriceExport implements FromQuery, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return ProductPrice::with(['product', 'uom'])
            ->where('status', 1)
            ->where('start_date', '<=', date('Y-m-d'))
            ->orderBy('product_id')
            ->orderBy('start_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'UOM ID',
            'UOM Name',
            'Retail Price',
            'Trade Price',
            'Pcs Per Carton',
            'Start Date'
        ];
    }

    public function map($price): array
    {
        return [
            $price->product_id,
            optional($price->product)->product_name,
            $price->uom_id,
            optional($price->uom)->uom_name,
            $price->retail_price,
            $price->trade_price,
            $price->pcs_per_carton,
            $price->start_date,
        ];
    }
}
