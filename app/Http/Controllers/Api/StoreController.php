<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreateRequest;
use App\Http\Requests\StoreUpdateRequest;
use App\Models\Store;
use App\Services\StoreService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __construct(StoreService $service)
    {
        $this->authorizeResource(Store::class, 'store');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->paginate($request);
        return response()->json($result);
    }

    public function store(StoreCreateRequest $request)
    {
        $result = $this->service->create($request);
        return response()->json($result, 201);
    }

    public function show(Request $request, Store $store)
    {
        $result = $this->service->get($request, $store);
        return response()->json($result);
    }

    public function update(StoreUpdateRequest $request, Store $store)
    {
        $result = $this->service->update($request, $store);
        return response()->json($result);
    }

    public function destroy(Request $request, Store $store)
    {
        $result = $this->service->delete($request, $store);
        return response()->json($result);
    }
}
