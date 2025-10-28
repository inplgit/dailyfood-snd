<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    use HasFactory;
    protected $fillable = ['rack_code','bar_code','status'];
    protected $primarykey = 'id'; 

    function scopeStatus($query)
      {
         return $query->where('status',1);
      }


}
