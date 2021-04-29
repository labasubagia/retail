<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductTypeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $brand = $this->route('product_type');
        return $this->user()->can('update', $brand);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string:max:255',
        ];
    }
}
