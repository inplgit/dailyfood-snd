<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\Common\CommonScope;
class SalesReturnData extends Model
{

    use HasFactory;
    protected $table = 'sales_return_data';
    protected $guarded = [];
    protected $primarykey = 'id';

    protected static function boot()
    {
        parent::boot();
        // Global Scope
        static::addGlobalScope(new CommonScope);
    }

    public function SalesReturn()
    {
       return $this->belongsTo(SalesReturn::class,'sales_return_id','id');
    }


    public function SalesOrderData()
    {
       return $this->belongsTo(SaleOrderData::class,'sales_order_data_id','id');
    }



}
