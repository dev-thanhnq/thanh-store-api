<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ImportProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\ImportRequest;
use App\Imports\ProductsImport;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    use ResponseTrait;

    public function index(Request $request) {
        $query = Product::query();
        if ($request->has('q') && strlen($request->input('q')) > 0 ) {
            $query->where('name', 'LIKE', "%" . $request->input('q') . "%")
            ->orWhere('sku', 'LIKE', "%" . $request->input('q') . "%");
        }

        if ($request->has('category_id') && strlen($request->input('category_id')) > 0) {
            $query->where('category_ids', '=' , $request->input('category_id'));
        }

        $products = $query->orderBy('created_at', 'DESC')->with('categories')->paginate(config('constants.products_per_page'));

        return $this->responseSuccess($products);

    }

    public function store(StoreProductRequest $request) {
        try {
            $product = new Product();
            $product->name = $request->input('name');
            $product->description = $request->input('description');
            $product->weight = (int)$request->input('weight');
            $product->original_price = (int)$request->input('original_price');
            $product->sale_price = (int)$request->input('sale_price');
            $product->quantity_in_stock = (int)$request->input('quantity_in_stock');
            $product->image = null;

            if ($request->has('sku')) {
                $countSku = Product::where('sku', $request->input('sku'))->count();
                if ($countSku > 0) {
                    $error = ['sku' => ['SKU đã tồn tại !']];
                    return $this->responseError('error', $error, 400);
                }
                $product->sku = $request->input('sku');
            }

            if ($request->hasFile('image')) {
                $path = Storage::disk('public')->putFile('images/products', $request->file('image'));
                $product->image = $path;
            }

            $product->save();

            if ($request->input('category_ids')) {
                $categoryIds = explode(',', $request->input('category_ids'));
                $product->categories()->attach($categoryIds);
            }
            ProductHistory::create([
                'product_id' => $product->_id,
                'creator_id' => auth()->id(),
                'quantity' => $product->quantity_in_stock,
                'quantity_in_stock' => $product->quantity_in_stock,
            ]);

            return $this->responseSuccess();

        } catch(Exception $e) {
            Log::error('Error store product', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }

    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);
            if ($product) {
                $countSku = Product::where('sku', $request->input('sku'))->where('_id', '<>', $id)->count();

                if ($countSku > 0) {
                    $error = ['sku' => ['SKU đã tồn tại !']];
                    return $this->responseError('error', $error, 400);
                }

                $product->sku = $request->input('sku');
                $product->name = $request->input('name');
                $product->weight = (int)$request->input('weight');
                $product->sale_price = (int)$request->input('sale_price');
                $product->original_price = (int)$request->input('original_price');
                $product->quantity_in_stock = (int)$request->input('quantity_in_stock');

                if ($request->has('description')) {
                    $product->description = $request->input('description');
                }
                if ($request->hasFile('image')) {
                    $path = Storage::disk('public')->putFile('images/products', $request->file('image'));
                    $product->image = $path;
                }

                if ($request->has('category_ids')) {
                    $categories = $product->categories;
                    $product->categories()->detach();
                    foreach($categories as $category) {
                        $category->products()->detach($product->_id);
                    }
                    $categoryIds = explode(',', $request->input('category_ids'));
                    $product->categories()->attach($categoryIds);
                }

                $product->save();
            } else {
                return $this->responseError('Product not found', [], 404);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update product', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function import(ImportProductRequest $request)
    {
        try {
            $products = Excel::toArray(new ProductsImport(), $request->file('file'));
            if (count($products)) {
                $products = $products[0];
                foreach ($products as $key => $product) {
                    if ($key > 0) {
                        $newProduct = new Product();
                        $newProduct->sku = Product::generateCode();
                        $newProduct->name = $product[0];
                        $newProduct->quantity_in_stock = (int)$product[1];
                        $newProduct->sale_price = (int)$product[2];
                        $newProduct->original_price = (int)$product[3];
                        $newProduct->weight = (int)$product[4];
                        $newProduct->description = null;
                        $newProduct->image = null;
                        $newProduct->save();

                        ProductHistory::create([
                            'product_id' => $newProduct->_id,
                            'creator_id' => auth()->id(),
                            'quantity' => (int)$product[1],
                            'quantity_in_stock' => (int)$product[1],
                        ]);
                    }
                }
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error import product', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        Cart::where('product_id', $id)->delete();
        ProductHistory::where('product_id', $id)->delete();
        $categories = $product->categories;
        foreach($categories as $category) {
            $category->products()->detach($product->_id);
        }
        $product->delete();
        return $this->responseSuccess();
    }

    public function getTemplateImportFile()
    {
        $file = public_path() . '/template/import_product_template.xlsx';
        return response()->download($file, 'import_product_template.xlsx');
    }

    public function importProduct(ImportRequest $request, $id){
        try {
        $product = Product::find($id);
        $product->quantity_in_stock += (int)$request->input('quantity');
        $product->save();

        ProductHistory::create([
            'product_id' => $product->_id,
            'creator_id' => auth()->id(),
            'quantity' => (int)$request->input('quantity'),
            'quantity_in_stock' => $product->quantity_in_stock,
        ]);

            return $this->responseSuccess();
        }
        catch (Exception $e) {
            Log::error('Error import product', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function show ($id){
        $product = Product::findOrFail($id)->load(['categories','histories.user','histories' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return $this->responseSuccess($product);
    }
}
