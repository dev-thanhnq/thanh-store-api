<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

class ChangeOrderStatusRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => ['required', Rule::in(Order::STATUS)],
        ];
    }

    public function attributes()
    {
        return [
            'status' => 'Trạng thái',
        ];
    }
}
