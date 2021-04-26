<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function register(UserRegisterRequest $request)
    {
        $result = $this->service->register($request);
        return response()->json($result, 201);
    }

    public function login(UserLoginRequest $request)
    {
        $result = $this->service->login($request);
        return response()->json([
            'token' => $result,
        ]);
    }

    public function logout(Request $request)
    {
        $result = $this->service->logout($request->user());
        return response()->json($result);
    }
}
