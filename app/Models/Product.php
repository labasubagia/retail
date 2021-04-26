<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'brand_id',
        'vendor_id',
        'product_type_id',
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function storeStocks()
    {
        return $this->hasMany(StoreStock::class);
    }

    public function scopeOfEnterprise($query, User $user)
    {
        if (!$user->enterprise_id) return $query;
        $product = $this->getTable();
        return $query->where("$product.enterprise_id", $user->enterprise_id);
    }

    public function scopeStock($query)
    {
        $product = $this->getTable();
        $stock = (new StoreStock)->getTable();
        return $query
            ->select(
                "$product.*",
                "$stock.stock",
                "$stock.store_id",
                "$stock.id as store_stock_id"
            )
            ->leftJoin($stock, "$stock.product_id", "$product.id");
    }
}
