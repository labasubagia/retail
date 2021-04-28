<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Services\OrderItemService;
use Illuminate\Support\Arr;

class OrderItemObserver
{
    public $afterCommit = true;

    public function __construct(OrderItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function created(OrderItem $orderItem)
    {
        $this->itemService->subtractStock($orderItem);
    }
}
