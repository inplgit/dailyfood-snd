<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Type extends Model
{
    use HasFactory;

    // default save username
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->username = (Auth::check())? Auth::user()->name : "Amir Murshad";
        });
    }

    // get active record
    function scopeStatus($query)
    {
       return $query->where('status',1);
    }

}
