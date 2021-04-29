<?php

namespace App\Services;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    public function __construct(Product $productModel)
    {
        $this->productModel = $productModel;
    }

    public function paginate(Request $request)
    {
        return $this->productModel->stock()->paginate($request->get('per_page', 10));
    }

    public function get(Request $request, Product $data)
    {
        return $this->productModel
            ->where('products.id', $data->id)
            ->stock()
            ->first();
    }

    public function create(ProductCreateRequest $request)
    {
        $payload = $request->only($this->productModel->getFillable());
        return Product::create(array_merge(
            $payload,
            ['enterprise_id' => $request->user()->enterprise_id]
        ));
    }

    public function update(ProductUpdateRequest $request, Product $data)
    {
        if (! $data) {
            return null;
        }
        $payload = $request->only($data->getFillable());
        $data->update($payload);
        return $data;
    }

    public function delete(Request $request, Product $data)
    {
        if (! $data) {
            return null;
        }
        $data->delete();
        return $data;
    }
}
