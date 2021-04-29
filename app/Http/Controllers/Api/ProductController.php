<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(ProductService $service)
    {
        $this->authorizeResource(Product::class, 'product');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->paginate($request);
        return response()->json($result);
    }

    public function store(ProductCreateRequest $request)
    {
        $result = $this->service->create($request);
        return response()->json($result, 201);
    }

    public function show(Request $request, Product $product)
    {
        $result = $this->service->get($request, $product);
        return response()->json($result);
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $result = $this->service->update($request, $product);
        return response()->json($result);
    }

    public function destroy(Request $request, Product $product)
    {
        $result = $this->service->delete($request, $product);
        return response()->json($result);
    }
}
