<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\ProductPrice;
use App\Models\UOM;
use App\Models\ProductFlavour;
use Auth;
use Illuminate\Support\Facades\DB;
class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $timestamp = true;


    // default save username
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->username = Auth::user()->name;
            $model->date = date('Y-m-d');
        });
    }

     public  function UOM()
    {
          return  $this->belongsTo(UOM::class,'uom_id');
    }

    public  function packing_uom()
    {
          return  $this->belongsTo(UOM::class,'packing_uom_id');
    }

    public function Category()
    {
      return  $this->belongsTo(Category::class);

    }

    public function Brand()
    {
     return   $this->belongsTo(Brand::class);
    }

    public function ProductType()
    {
      return  $this->belongsTo(ProductType::class);
    }


    public function product_flavour()
    {
      return  $this->hasMany(ProductFlavour::class)->where('status', 1);
    }


    public  function product_price()
    {
          return  $this->hasMany(ProductPrice::class)->where('status' , 1);
    }

 public function tsoTargetNew($tso_id, $month , $type ,$flavour)
     {
          $month = date("m",strtotime($month));
          $tso_target = TSOTarget::where('tso_id', $tso_id)
            ->whereMonth('month', $month)
            ->where('product_id', $this->id)
            ->where('type' , $type)
            ->where('flavour_id' , $flavour)
            ->first();
            if ($type == 1) {
                return $tso_target->qty ?? 0;
            }
            else{
                return $tso_target->amount ?? 0;
            }

     }

      // get active data
      function scopeStatus($query)
      {
         return $query->where('status',1);
      }


      // get unique no
      function scopeUniqueNo($query)
      {
       $id = $query->max('id')+1;
      return  $number = sprintf('%03d',$id);

      }

      // get all brand
      function get_all_brand()
      {
           return Brand::status()->get();
      }
      function scopeStatusScheme($query)
      {
          return $query->where('status',1)->where('product_type_id',4);
      }
public function retailPrice()
{
    return $this->hasOne(ProductPrice::class, 'product_id')
                ->where('status', 1)
                ->orderByDesc('id'); // Optional: latest price
}

      // get all category
      function get_all_category()
      {
           return Category::status()->get();
      }

      // get all product type

      function get_all_product_type()
      {
           return ProductType::status()->get();
      }


      // get all uom
      function get_all_uom()
      {
           return UOM::status()->get();
      }


      // get all product with relation
      function scopeProductRelation($query)
      {
          return   $query->with(['UOM:id,uom_name','Category:id,name','ProductType:id,type_name','Brand:id,brand_name']);
      }

     public function tsoTarget($tso_id, $month , $type)
     {
          $month = date("m",strtotime($month));
          $tso_target = TSOTarget::where('tso_id', $tso_id)
            ->whereMonth('month', $month)
            ->where('product_id', $this->id)
            ->where('type' , $type)
            ->first();
            if ($type == 1) {
                return $tso_target->qty ?? 0;
            }
            else{
                return $tso_target->amount ?? 0;
            }

     }

     public function tsoTargetType($tso_id, $month)
     {
        $month = date("m",strtotime($month));
        $tso_target = TSOTarget::where('tso_id', $tso_id)
          ->whereMonth('month', $month)
          ->where('product_id', $this->id)
          ->first();
          return $tso_target->type ?? 0;
     }
     public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_id', 'id');
    }
}
