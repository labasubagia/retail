<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Auth;
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

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

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

    public function scopeStock($query)
    {
        $product = $this->getTable();
        $stock = (new StoreStock)->getTable();
        $user = Auth::user();
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
