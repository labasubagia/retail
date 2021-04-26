<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\StoreScope;

class TransactionOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'product_id',
        'store_id',
        'user_id',
        'transaction_order_id',
        'amount',
        'subtotal',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new StoreScope);
    }

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(TransactionOrder::class);
    }
}
