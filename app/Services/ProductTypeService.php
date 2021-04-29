<?php

namespace App\Services;

use App\Http\Requests\ProductTypeCreateRequest;
use App\Http\Requests\ProductTypeUpdateRequest;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeService
{
    public function __construct(ProductType $productTypeModel)
    {
        $this->productTypeModel = $productTypeModel;
    }

    public function paginate(Request $request)
    {
        return $this->productTypeModel->paginate($request->get('per_page', 10));
    }

    public function get(Request $request, ProductType $data)
    {
        return $data;
    }

    public function create(ProductTypeCreateRequest $request)
    {
        $payload = $request->only($this->productTypeModel->getFillable());
        return $this->productTypeModel->create(array_merge(
            $payload,
            ['enterprise_id' => $request->user()->enterprise_id]
        ));
    }

    public function update(ProductTypeUpdateRequest $request, ProductType $data)
    {
        if (! $data) {
            return null;
        }
        $payload = $request->only($data->getFillable());
        $data->update($payload);
        return $data;
    }

    public function delete(Request $request, ProductType $data)
    {
        if (! $data) {
            return null;
        }
        $data->delete();
        return $data;
    }
}
