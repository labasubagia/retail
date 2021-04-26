<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\StoreScope;

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

}
