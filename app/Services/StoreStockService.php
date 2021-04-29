<?php

namespace App\Services;

use App\Http\Requests\StoreStockUpsertRequest;
use App\Models\StoreStock;
use Illuminate\Support\Arr;

class StoreStockService
{
    public function __construct(StoreStock $storeStockModel)
    {
        $this->storeStockModel = $storeStockModel;
    }

    public function upsert(StoreStockUpsertRequest $request, StoreStock $storeStock)
    {
        $user = $request->user();
        $payload = array_merge(
            $request->only($this->storeStockModel->getFillable()),
            ['enterprise_id' => $user->enterprise_id, 'store_id' => $user->store_id],
        );
        $find = $storeStock->id
            ? ['id' => $storeStock->id]
            : Arr::only($payload, ['enterprise_id', 'store_id', 'product_id']);
        return $this->storeStockModel->updateOrCreate($find, $payload);
    }
}
