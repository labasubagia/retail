<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'product_id',
        'store_id',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
        'product_id' => 'integer',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeOfEnterprise($query, User $user)
    {
        return $query->where('enterprise_id', $user->enterprise_id);
    }

    public function scopeOfStore($query, User $user)
    {
        $stock = $this->getTable();
        return $query
            ->where("$stock.enterprise_id", $user->enterprise_id)
            ->where("$stock.store_id", $user->store_id);
    }
}
