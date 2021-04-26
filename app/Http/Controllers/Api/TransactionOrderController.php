<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionOrderCreateRequest;
use App\Models\TransactionOrder;
use App\Services\TransactionOrderService;
use Illuminate\Http\Request;

class TransactionOrderController extends Controller
{
    public function __construct(TransactionOrderService $service)
    {
        $this->authorizeResource(TransactionOrder::class, 'order');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->paginate($request);
        return response()->json($result);
    }

    public function store(TransactionOrderCreateRequest $request)
    {
        $result = $this->service->create($request);
        return response()->json($result, 201);
    }

    public function show(Request $request, TransactionOrder $order)
    {
        $result = $this->service->get($request, $order);
        return response()->json($result);
    }
}
