<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeOfEnterprise($query, User $user)
    {
        return $query->where('enterprise_id', $user->enterprise_id);
    }

    public function getIsNotEmployeeAttribute()
    {
        return !$this->enterprise_id && !$this->store_id;
    }

    public function getIsOnlyEnterpriseEmployeeAttribute()
    {
        return $this->enterprise_id && !$this->store_id;
    }

    public function getIsStoreEmployeeAttribute()
    {
        return $this->enterprise_id && $this->store_id;
    }
}
