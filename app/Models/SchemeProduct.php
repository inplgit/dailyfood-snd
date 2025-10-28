<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchemeProductData;
use Auth;

class SchemeProduct extends Model
{
    use HasFactory;
    protected $table = 'scheme_product';
    protected $guarded = [];
    protected $primarykey = 'id';
    protected $appends = ['SchemeProductData'];

     // default save username
     protected static function booted()
     {
         static::creating(function ($model) {
             $model->username = Auth::user()->name;
             $model->date = date('Y-m-d');
         });
     }

 public function getSchemeProductDataAttribute()
    {
        return $this->hasMany(SchemeProductData::class,'scheme_id')->get();
    }

    public function SchemeProductData()
    {
        return $this->hasMany(SchemeProductData::class,'scheme_id');
    }

     // get active data
    function scopeStatus($query)
    {
    return $query->where('scheme_product.status',1);
    }

    function scopeActive($query)
    {
    return $query->where('scheme_product.active',1);
    }

}
