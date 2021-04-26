<?php

namespace App\Observers;

use App\Models\TransactionOrderItem;
use App\Services\TransactionOrderItemService;
use Illuminate\Support\Arr;

class TransactionOrderItemObserver
{
    public $afterCommit = true;

    public function __construct(TransactionOrderItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function created(TransactionOrderItem $transactionOrderItem)
    {
        $this->itemService->subtractStock($transactionOrderItem);
    }
}
