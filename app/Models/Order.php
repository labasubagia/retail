<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\StoreScope;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'store_id',
        'user_id',
        'total',
    ];

    protected $casts = [
        'total' => 'integer',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new StoreScope);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
