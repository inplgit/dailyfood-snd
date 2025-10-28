<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductType extends Model
{
    use HasFactory;

    public $table ='product_types';
    protected $fillable = ['type_name','status'];
    protected $primarykey = 'id';


    // default save username
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->username = Auth::user()->name;
        });
    }

    // get active record
    function scopeStatus($query)
    {
       return $query->where('status',1);
    }


    public function Product()
    {
        return $this->hasMany(Product::class);
    }
}
