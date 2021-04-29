<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\StoreStock;

class OrderItemService
{
    public function __construct(
        OrderItem $itemModel,
        StoreStock $stockModel
    ) {
        $this->itemModel = $itemModel;
        $this->stockModel = $stockModel;
    }

    public function subtractStock(OrderItem $data)
    {
        $filter = $data->only(['enterprise_id', 'store_id', 'product_id']);
        $amount = (int)$data->amount;
        $stock = $this->stockModel->where($filter)->where('stock', '>=', $amount)->first();
        if (! $stock) {
            return;
        }
        $stock->decrement('stock', $amount);
        return $stock;
    }
}
