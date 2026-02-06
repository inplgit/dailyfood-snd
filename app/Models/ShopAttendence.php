<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopAttendence extends Model
{
    protected $fillable = ['distributor_id','tso_id','shop_id','check_in','check_out','sync_date_time'];
    use HasFactory;

    public function tso()
    {
        return $this->belongsTo(TSO::class,'tso_id','id');
    }


    public function distributor()
    {
        return $this->belongsTo(Distributor::class,'distributor_id','id');
    }

}
