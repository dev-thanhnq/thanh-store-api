<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class ChangePasswordRequest extends BaseRequest
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
            'password' => 'min:4',
            'new_password' => 'min:4',
        ];
    }

    public function attributes()
    {
        return [
            'password' => 'Mật khẩu hiện tại',
            'new_password' => 'Mật khẩu mới',
        ];
    }
}
