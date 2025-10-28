<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrderReturn extends Model
{
    use HasFactory;

       protected $table = 'sale_order_returns';

    protected $fillable = [
        'user_id',
        'user_name',
        'distributor_id',
        'return_date',
        'return_reason',
        'return_no',
        'shop_id',
        'excecution', // make sure this exists in your DB
        'created_at',
        'updated_at',
    ];

    public function returnDetails()
    {
        return $this->hasMany(SaleOrderReturnDetail::class, 'sale_order_return_id');
    }

    public function distributor()
{
    return $this->belongsTo(Distributor::class, 'distributor_id');
}

// App\Models\Distributor.php
public function tso()
{
    return $this->belongsTo(TSO::class, 'tso_id');
}


public function shop()
{
    return $this->belongsTo(Shop::class, 'shop_id');
}






}
