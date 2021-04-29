<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandCreateRequest;
use App\Http\Requests\BrandUpdateRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(BrandService $service)
    {
        $this->authorizeResource(Brand::class, 'brand');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->paginate($request);
        return response()->json($result);
    }

    public function store(BrandCreateRequest $request)
    {
        $result = $this->service->create($request);
        return response()->json($result, 201);
    }

    public function show(Request $request, Brand $brand)
    {
        $result = $this->service->get($request, $brand);
        return response()->json($result);
    }

    public function update(BrandUpdateRequest $request, Brand $brand)
    {
        $result = $this->service->update($request, $brand);
        return response()->json($result);
    }

    public function destroy(Request $request, Brand $brand)
    {
        $result = $this->service->delete($request, $brand);
        return response()->json($result);
    }
}
