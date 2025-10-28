<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchemeProductPcs;
use App\Models\Product;

class SchemeProductDataPcs extends Model
{
    use HasFactory;
    protected $table = 'scheme_product_data_pcs';
    protected $guarded = [];
    protected $primarykey = 'id';

    public function SchemeProduct()
    {
       return $this->belongsTo(SchemeProductPcs::class,'scheme_id','id');
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
