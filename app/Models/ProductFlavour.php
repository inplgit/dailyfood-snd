<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductFlavour extends Model
{
    use HasFactory;
    protected $table = 'product_flavours';
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $timestamp = true;

    public  function product()
    {
          return  $this->belongsTo(Product::class,'product_id');
    }
}
