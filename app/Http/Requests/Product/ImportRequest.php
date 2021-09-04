<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;

class ImportRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantity' => 'required|integer|gte:1',
        ];
    }

    public function attributes()
    {
        return [
            'quantity' => 'Số lượng',
        ];
    }
}
