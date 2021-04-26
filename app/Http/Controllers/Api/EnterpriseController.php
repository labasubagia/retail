<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnterpriseCreateRequest;
use App\Http\Requests\EnterpriseUpdateRequest;
use App\Models\Enterprise;
use App\Services\EnterpriseService;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    public function __construct(EnterpriseService $service)
    {
        $this->authorizeResource(Enterprise::class, 'enterprise');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->paginate($request);
        return response()->json($result);
    }

    public function store(EnterpriseCreateRequest $request)
    {
        $result = $this->service->create($request);
        return response()->json($result, 201);
    }

    public function show(Request $request, Enterprise $enterprise)
    {
        $result = $this->service->get($request, $enterprise);
        return response()->json($result);
    }

    public function update(EnterpriseUpdateRequest $request, Enterprise $enterprise)
    {
        $result = $this->service->update($request, $enterprise);
        return response()->json($result);
    }

    public function destroy(Request $request, Enterprise $enterprise)
    {
        $result = $this->service->delete($request, $enterprise);
        return response()->json($result);
    }
}
