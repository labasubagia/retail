<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function current(Request $request)
    {
        $result = $request->user();
        return response()->json($result);
    }
}
