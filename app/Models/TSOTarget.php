<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TSOTarget extends Model
{
    use HasFactory;
    protected $table = "tso_targets";
    protected $guarded = [];
}
