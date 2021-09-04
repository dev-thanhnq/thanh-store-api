<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class ChangeProfileRequest extends BaseRequest
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
            'name' => 'required',
            'phone' => 'required',
            'email' => 'email|nullable',
            'address' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'email' => 'Email',
            'address' => 'Địa chỉ',
        ];
    }
}
