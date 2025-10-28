<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrderReturnDetail extends Model
{
    use HasFactory;

      protected $table = 'sale_order_return_details';

    protected $fillable = [
        'return_id',
        'product_id',
        'quantity',
        'reason',
        'damage_photo',
        'expiry_date',
        'created_at',
        'updated_at',
    ];

    public function returnOrder()
    {
        return $this->belongsTo(SaleOrderReturn::class, 'return_id');
    }

public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}

public function flavour()
{
    return $this->belongsTo(ProductFlavour::class, 'flavour_id');
}


}
