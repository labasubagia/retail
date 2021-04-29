<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTypeCreateRequest;
use App\Http\Requests\ProductTypeUpdateRequest;
use App\Models\ProductType;
use App\Services\ProductTypeService;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function __construct(ProductTypeService $service)
    {
        $this->authorizeResource(ProductType::class, 'product_type');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->paginate($request);
        return response()->json($result);
    }

    public function store(ProductTypeCreateRequest $request)
    {
        $result = $this->service->create($request);
        return response()->json($result, 201);
    }

    public function show(Request $request, ProductType $productType)
    {
        $result = $this->service->get($request, $productType);
        return response()->json($result);
    }

    public function update(ProductTypeUpdateRequest $request, ProductType $productType)
    {
        $result = $this->service->update($request, $productType);
        return response()->json($result);
    }

    public function destroy(Request $request, ProductType $productType)
    {
        $result = $this->service->delete($request, $productType);
        return response()->json($result);
    }
}
