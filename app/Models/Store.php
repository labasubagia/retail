<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\EnterpriseScope;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'enterprise_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

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
}
