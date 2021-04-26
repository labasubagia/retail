<?php

namespace App\Services;

use App\Models\Brand;
use App\Http\Requests\BrandCreateRequest;
use App\Http\Requests\BrandUpdateRequest;
use Illuminate\Http\Request;

class BrandService
{
    public function __construct(Brand $brandModel)
    {
        $this->brandModel = $brandModel;
    }

    public function paginate(Request $request)
    {
        return $this->brandModel
            ->ofEnterprise($request->user())
            ->paginate($request->get('per_page', 10));
    }

    public function get(Request $request, Brand $data)
    {
        return $data;
    }

    public function create(BrandCreateRequest $request)
    {
        $payload = $request->only($this->brandModel->getFillable());
        return $this->brandModel->create(array_merge(
            $payload,
            ['enterprise_id' => $request->user()->enterprise_id]
        ));
    }

    public function update(BrandUpdateRequest $request, Brand $data)
    {
        if (!$data) return null;
        $payload = $request->only($data->getFillable());
        $data->update($payload);
        return $data;
    }

    public function delete(Request $request, Brand $data) {
        if(!$data) return null;
        $data->delete();
        return $data;
    }
}
