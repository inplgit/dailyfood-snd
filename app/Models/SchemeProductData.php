<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchemeProduct;
use App\Models\Product;

class SchemeProductData extends Model
{
    use HasFactory;
    protected $table = 'scheme_product_data';
    protected $guarded = [];
    protected $primarykey = 'id';



    public function SchemeProduct()
    {
       return $this->belongsTo(SchemeProduct::class,'scheme_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    function scopeStatus($query)
    {
       return $query->where('status',1);
    }

}
