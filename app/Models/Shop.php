<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\UsersLocation;
use App\Models\ActivityLog;
use App\Models\TSO;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shops';
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $timestamp = true;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->username = Auth::user()->name??'Amir Murshad';

        });
    }


public function tsos()
{
    return $this->belongsToMany(
        Tso::class,   // related model
        'shop_tso',   // pivot table
        'shop_id',    // FK on pivot -> shops.id
        'tso_id'      // FK on pivot -> tsos.id
    );
}

    function scopeUniqueNo($query)
    {
     $id = $query->max('id')+1;
    return  $number = sprintf('%03d',$id);

    }

     function scopeStatus($query)
      {
         return $query->where('status',1);
      }

      function scopeActive($query)
      {
         return $query->where('active',1);
      }

      public function Distributor()
      {
          return $this->belongsTo(Distributor::class);
      }

      public function TSO()
      {
          return $this->belongsTo(TSO::class,'tso_id');
      }


      public function Route()
      {
          return $this->belongsTo(Route::class);
      }

      public function Zone()
      {
          return $this->belongsTo(Zone::class,'shop_zone_id');
      }

    public function subroutes()
      {
          return $this->belongsTo(SubRoutes::class,'sub_route_id');
      }

      public function ShopOutstanding()
      {
        return   $this->hasOne(ShopOutsanding::class);
      }

      public function Sales()
      {
        return   $this->hasOne(SaleOrder::class,'shop_id');
      }

      public function usersLocation()
    {
        return $this->morphMany(UsersLocation::class ,'table');
    }

    public function activityLog()
    {
        return $this->morphMany(ActivityLog::class ,'table');
    }

     public function cities()
    {
      return  $this->belongsTo(City::class,'city');
    }

public function shop()
{
    return $this->belongsToMany(Shop::class, 'shop_tso', 'tso_id', 'shop_id');
}


}
