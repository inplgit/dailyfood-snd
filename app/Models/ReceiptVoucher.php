<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptVoucher extends Model
{
    use HasFactory;
    protected $guarded = [];

    /** Relationships */
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
    public function tso()
    {
        return $this->belongsTo(TSO::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'delivery_man_id', 'id');
    }
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
    function scopeStatus($query)
      {
         return $query->where('status',1);
      }

      public function UsersDistributor()
      {
      return  $this->hasMany(UsersDistributors::class,'distributor_id','distributor_id');
      }
       
}
