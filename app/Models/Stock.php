<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Stock extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     "distributor_id",
    //     "stock_received_type",
    //     "voucher_date",
    //     "stock_type",
    //     "product_id",
    //     'remarks',
    //     "qty",
    // ];
    protected $guarded = [];
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

     public function product()
     {
        return $this->BelongsTo(Product::class,'product_id');
     }


    public function Distributor()
    {
        return $this->BelongsTo(Distributor::class);
    }


    public function DistributorSole()
    {
        return $this->BelongsTo(Distributor::class,'distributor_sole');
    }
}
