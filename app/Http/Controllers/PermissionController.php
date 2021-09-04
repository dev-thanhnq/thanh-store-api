<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use ResponseTrait;

    public function index (Request $request)
    {
        $query = PermissionGroup::query();

        if ($request->has('q') && strlen($request->input('q')) > 0 ) {
            $query->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }
        $permissions = $query->with('permissions')->get();
            
        return $this->responseSuccess($permissions);
    }
}
