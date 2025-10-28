<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\Common\CommonScope;
class SalesReturn extends Model
{
    use HasFactory;
    protected $table = 'sales_returns';
    protected $guarded = [];
   
    
    protected static function boot()
    {
        parent::boot();
        // Global Scope
        static::addGlobalScope(new CommonScope);
    }

    public function SalesOrder()
    {
       return $this->belongsTo(SaleOrder::class,'so_id','id');
    }
    function scopeStatus($query)
    {
        return $query->where('status', 1);
    }
    public function SalesReturnData()
    {
        return $this->hasMany(SalesReturnData::class,'sales_return_id');
    }

    function scopeUniqueNo($query)
    {
     $id = $query->max('id')+1;
    return  $number = 'sr'.sprintf('%03d',$id);

    }

    public function usersLocation()
    {
        return $this->morphMany(UsersLocation::class ,'table_type');
    }
}
