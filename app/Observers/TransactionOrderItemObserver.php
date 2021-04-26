<?php

namespace App\Observers;

use App\Models\TransactionOrderItem;
use App\Models\StoreStock;
use Illuminate\Support\Arr;

class TransactionOrderItemObserver
{
    public $afterCommit = true;

    public function __construct(StoreStock $stockModel)
    {
        $this->stockModel = $stockModel;
    }

    public function created(TransactionOrderItem $transactionOrderItem)
    {
        $this->subtractStock($transactionOrderItem);
    }

    private function subtractStock(TransactionOrderItem $transactionOrderItem)
    {
        $filter = $transactionOrderItem->only(['enterprise_id', 'store_id', 'product_id']);
        $amount =  (int)$transactionOrderItem->amount;
        $stock = $this->stockModel->where($filter)->where('stock', '>=', $amount)->first();
        if (!$stock) return;
        $stock->decrement('stock', $amount);
    }
}
