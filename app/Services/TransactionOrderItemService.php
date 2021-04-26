<?php

namespace App\Services;

use App\Models\TransactionOrderItem;
use App\Models\StoreStock;
use Illuminate\Support\Arr;
use Exception;

class TransactionOrderItemService
{
    public function __construct(
        TransactionOrderItem $itemModel,
        StoreStock $stockModel
    )
    {
        $this->itemModel = $itemModel;
        $this->stockModel = $stockModel;
    }

    public function subtractStock(TransactionOrderItem $data)
    {
        $filter = $data->only(['enterprise_id', 'store_id', 'product_id']);
        $amount =  (int)$data->amount;
        $stock = $this->stockModel->where($filter)->where('stock', '>=', $amount)->first();
        if (!$stock) return;
        $stock->decrement('stock', $amount);
        return $stock;
    }
}
