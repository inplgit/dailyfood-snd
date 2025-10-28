<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Category extends Model
{
    use HasFactory;

    protected $table ='categories';
    protected $fillable = ['name','username'];
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->username = Auth::user()->name;
        });
    }
    protected $primarykey = 'id';
    function scopeStatus($query)
    {
       return $query->where('status',1);
    }

   public function Product()
   {
        return $this->hasMany(Product::class);
   }
}
