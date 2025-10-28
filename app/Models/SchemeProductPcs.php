<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchemeProductDataPcs;
use Auth;

class SchemeProductPcs extends Model
{
    use HasFactory;
    protected $table = 'scheme_product_pcs';
    protected $guarded = [];
    protected $primarykey = 'id';

   protected $appends = ['SchemeProductDataPcs'];

     // default save username
     protected static function booted()
     {
         static::creating(function ($model) {
             $model->username = Auth::user()->name;
             $model->date = date('Y-m-d');
         });
     }
  public function getSchemeProductDataPcsAttribute()
     {
         return $this->hasMany(SchemeProductDataPcs::class,'scheme_id')->get();
     }
    public function SchemeProductData()
    {
        return $this->hasMany(SchemeProductDataPcs::class,'scheme_id');
    }

     // get active data
    function scopeStatus($query)
    {
    return $query->where('scheme_product_pcs.status',1);
    }

    function scopeActive($query)
    {
    return $query->where('scheme_product_pcs.active',1);
    }
}
