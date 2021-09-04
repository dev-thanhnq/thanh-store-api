<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Traits\ResponseTrait;

class CategoryController extends Controller
{
    use ResponseTrait;

    public function index (Request $request){
        $query = Category::query();

        if ($request->has('q') && strlen($request->input('q')) > 0 ) {
            $query->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }

        $categories = $query->orderBy('created_at', 'DESC')->paginate(config('constants.per_page'));

        return $this->responseSuccess($categories);
    }

    public function store (StoreCategoryRequest $request)
    {
        try {
            $category = new Category();
            $category->name = $request->input('name');
            $category->description = $request->input('description');
            $category->save();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error store category', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return $this->responseSuccess();
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = Category::find($id);
            $category->name = $request->input('name');

            if ($request->has('description')) {
                $category->description = $request->input('description');
            }
            $category->save();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update category', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function getAllCategories()
    {
        $categories = Category::orderBy('created_at','DESC')->get();
        return $this->responseSuccess($categories);
    }
}
