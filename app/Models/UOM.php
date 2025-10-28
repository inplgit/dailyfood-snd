<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class UOM extends Model
{
    use HasFactory;

    public $table ='uom';
    protected $fillable = ['uom_name','status','username'];
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


    public  function product()
    {
          return  $this->hasMany(Product::class);
    }
}
