<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOutsanding extends Model
{
    use HasFactory;

    protected $table = 'shops_outstandings';
    protected $guarded = [];
    protected $primaryKey = 'shop_id';


    public function Shop()
    {
       return $this->belongTo(Shop::class);
    }


  
}
