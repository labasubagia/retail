<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $product = $this->route('product');
        return $this->user()->can('update', $product);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'brand_id' => 'exists:brands,id',
            'vendor_id' => 'exists:vendors,id',
            'product_type_id' => 'exists:product_types,id',
            'name' => 'string|max:255',
            'price' => 'integer|min:0',
        ];
    }
}
