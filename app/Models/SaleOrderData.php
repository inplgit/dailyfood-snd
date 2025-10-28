<?php

namespace App\Models;

use App\Scopes\Common\CommonScope;
use App\Models\SalesReturnData;
use App\Models\Product;
use App\Models\SchemeProduct;
use App\Models\SchemeProductPcs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Helpers\MasterFormsHelper;


class SaleOrderData extends Model
{

    use HasFactory;
    protected $guarded = [];
    protected $appends = ['product','ShemeProduct','SchemeProductPcs','ProductFlavour' , 'SaleTypeName'];

    public $master;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes); // Always call the parent constructor

        // Initialize the helper
        $this->master = new MasterFormsHelper();
    }

    protected static function boot()
    {
        parent::boot();
        // Global Scope
        // static::addGlobalScope(new CommonScope);
    }
    function scopeStatus($query)
    {
        return $query->where('status', 1);
    }

    public function getQtyAttribute($value)
    {
        return (string) round($value); // Rounds the qty to the nearest integer
    }

    public function getProductAttribute()
    {
        return $this->belongsTo(Product::class,'product_id')->with(['product_flavour'])->select('product_name','retail_price','retail_price_packing','retail_price_carton','trade_price','trade_price_packing','trade_price_carton','id')->first();
    }
    public function getProductFlavourAttribute()
    {
        return $this->belongsTo(ProductFlavour::class,'flavour_id')->select('flavour_name','id')->first();
    }

    public function getSaleTypeNameAttribute()
    {
        // dd($this->sale_type , $this->product_id);
        return $this->master->uom_name($this->sale_type);
        // switch ($this->sale_type) {
        //     case 1:
        //         return $this->master->product_uom($this->product_id) ;
        //     case 2:
        //         return $this->master->product_packing_uom($this->product_id);
        //     case 3:
        //         return 'Carton';
        //     default:
        //         return 'Unknown';
        // }
    }

    public function getShemeProductAttribute()
    {
        return $this->belongsTo(SchemeProduct::class,'scheme_id')->first();
    }
  public function getSchemeProductPcsAttribute()
    {
        return $this->belongsTo(SchemeProductPcs::class,'scheme_id_pcs')->first();
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_flavour()
    {
        return $this->belongsTo(ProductFlavour::class , 'flavour_id');
    }

    public function scheme()
    {
        return $this->belongsTo(SchemeProduct::class , 'scheme_id');
    }
    public function SchmeProduct()
    {
        return $this->belongsTo(Product::class,'sheme_product_id');
    }
    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class, 'so_id', 'id');
    }

    public function SalesReturnData()
    {
        return $this->hasMany(SalesReturnData::class,'sales_order_data_id','id');
    }


}
