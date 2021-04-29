<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'brand_id' => 'required|exists:brands,id',
            'vendor_id' => 'required|exists:vendors,id',
            'product_type_id' => 'required|exists:product_types,id',
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ];
    }
}
