<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockUpsertRequest;
use App\Models\StoreStock;
use App\Services\StoreStockService;

class StoreStockController extends Controller
{
    public function __construct(StoreStockService $service)
    {
        $this->service = $service;
    }

    public function upsert(StoreStockUpsertRequest $request, StoreStock $storeStock)
    {
        $result = $this->service->upsert($request, $storeStock);
        return response()->json($result);
    }
}
