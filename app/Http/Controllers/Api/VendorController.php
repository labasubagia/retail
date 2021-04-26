<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorCreateRequest;
use App\Http\Requests\VendorUpdateRequest;
use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct(VendorService $service)
    {
        $this->authorizeResource(Vendor::class, 'vendor');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->paginate($request);
        return response()->json($result);
    }

    public function store(VendorCreateRequest $request)
    {
        $result = $this->service->create($request);
        return response()->json($result, 201);
    }

    public function show(Request $request, Vendor $vendor)
    {
        $result = $this->service->get($request, $vendor);
        return response()->json($result);
    }

    public function update(VendorUpdateRequest $request, Vendor $vendor)
    {
        $result = $this->service->update($request, $vendor);
        return response()->json($result);
    }

    public function destroy(Request $request, Vendor $vendor)
    {
        $result = $this->service->delete($request, $vendor);
        return response()->json($result);
    }
}
