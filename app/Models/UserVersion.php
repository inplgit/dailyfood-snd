<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVersion extends Model
{
    use HasFactory;

    protected $table = 'user_versions'; 

    protected $fillable = [
        'user_id',
        'app_version',
        'ip_address',
    ];
}
