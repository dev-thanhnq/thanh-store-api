<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;

class UpdateProductRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sku' => 'required',
            'name' => 'required',
            'image' => 'image|mimes:jpg,png,jpeg,svg|max:10000',
            'weight' => 'numeric|gte:0',
            'sale_price' => 'required|numeric|gte:0',
            'original_price' => 'required|numeric|gte:0',
            'quantity_in_stock' => 'required|numeric|gte:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'sku' => 'SKU',
            'name' => 'Tên sản phẩm',
            'image' => 'Ảnh',
            'weight' => 'Khối lượng',
            'sale_price' => 'Giá bán',
            'original_price' => 'Giá nhập', 
            'quantity_in_stock' => 'Số lượng tồn kho',
        ];
    }
}
