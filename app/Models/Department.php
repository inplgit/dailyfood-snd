<?php

namespace App\Models;

use App\Scopes\Common\CommonScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();
 
        static::addGlobalScope(new CommonScope);
    }
}
