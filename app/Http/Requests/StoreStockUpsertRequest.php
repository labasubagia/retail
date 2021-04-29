<?php

namespace App\Http\Requests;

use App\Models\StoreStock;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockUpsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $storeStock = $this->route('store_stock');
        return $storeStock
            ? $this->user()->can('update', $storeStock)
            : $this->user()->can('create', StoreStock::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0',
        ];
    }
}
