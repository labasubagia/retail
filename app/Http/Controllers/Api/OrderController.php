<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCreateRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(OrderService $service)
    {
        $this->authorizeResource(Order::class, 'order');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->paginate($request);
        return response()->json($result);
    }

    public function store(OrderCreateRequest $request)
    {
        $result = $this->service->create($request);
        return response()->json($result, 201);
    }

    public function show(Request $request, Order $order)
    {
        $result = $this->service->get($request, $order);
        return response()->json($result);
    }
}
