<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'image',
        'username'
    ];
    protected $appends = ['attendence'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAttendenceAttribute()
    {
        $attendence = $this->hasOne(Attendence::class, 'user_id')->where('out', null)->latest()->first();
        return $attendence??null;
        // return $attendence??(object)[];
    }
    // public function getTsoDataAttribute()
    // {
    //     $tso = $this->hasOne(TSO::class, 'user_id')->select('id','name', 'tso_code', 'emp_id','phone')->first();
    //     return $tso??[];
    // }
    function scopeStatus($query)
    {
       return $query->where('status',1);
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

public function shops()
{
    return $this->belongsToMany(Shop::class, 'shop_tso', 'tso_id', 'shop_id');
}

    public function tso()
    {
        return $this->hasOne(TSO::class);
    }


    public function user_detail()
    {
        return $this->hasOne(UserDetail::class);
    }


    public function Role()
    {
        return $this->hasMany(TSO::class);
    }

    public function Type()
    {
        return $this->belongsTo(Type::class,'user_type');
    }


    public function attendence()
    {
        return $this->hasMany(Attendence::class);
    }

    public function shopVisit()
    {
        return $this->hasMany(ShopVisit::class);
    }

    public function salesOrder()
    {
        return $this->hasMany(SaleOrder::class, 'user_id');
    }
    public function salesReturn()
    {
        return $this->hasMany(SalesReturn::class, 'user_id');
    }

    public function distributors()
    {
        return $this->belongsToMany(Distributor::class, 'users_distributors', 'user_id', 'distributor_id');
    }

    public function usersLocation()
    {
        return $this->morphMany(UsersLocation::class ,'table_type');
    }


}
