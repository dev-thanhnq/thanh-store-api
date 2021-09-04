<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseRequest;

class StoreOrderRequest extends BaseRequest
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
            'customer_id' => 'required',
            'receiver' => 'required',
            'receiver_phone' => 'required',
            'delivery_address' => 'required'
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
            'customer_id' => 'khách hàng',
            'receiver' => 'tên người nhận',
            'receiver_phone' =>'số điện thoại người nhận',
            'delivery_address' => 'địa chỉ giao hàng'
        ];
    }
}
