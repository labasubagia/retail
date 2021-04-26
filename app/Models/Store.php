<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'enterprise_id',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(TransactionOrder::class);
    }

    public function orderItems()
    {
        return $this->hasMany(TransactionOrder::class);
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class, (new StoreStock)->getTable(), 'store_id', 'product_id'
        );
    }

    public function scopeOfEnterprise($query, User $user)
    {
        return $query->where('enterprise_id', $user->enterprise_id);
    }
}
