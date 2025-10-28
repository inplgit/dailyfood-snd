<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\UOM;

class ProductPrice extends Model
{
    use HasFactory;
    protected $table = 'product_prices';
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $timestamp = true;

    public  function product()
    {
          return  $this->belongsTo(Product::class,'product_id');
    }
    public  function uom()
    {
          return  $this->belongsTo(UOM::class,'uom_id');
    }


}
