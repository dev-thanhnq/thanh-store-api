<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreOrderRequest;
use App\Http\Requests\Cart\UpdateCartProductRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;
use Carbon\Carbon;

class CartController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $cart = Cart::with('product')->get();

        return $this->responseSuccess($cart);
    }

    public function addCart($id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                $message = 'Sản phẩm không tồn tại';
                return $this->responseError($message, [], 400, config('error_code.not_found'));
            }

                $cartItem = Cart::where('product_id', $id)->first();
                if ($cartItem) {
                    $quantity = $cartItem->quantity + 1;
                    if ($product->quantity_in_stock < $quantity) {
                        $message = 'Tồn kho của sản phẩm không đủ';
                        return $this->responseError($message, [$product], 400, config('error_code.quantity_invalid'));
                    }
                    $cartItem->quantity = $quantity;
                    $cartItem->save();
                } else {
                    if ($product->quantity_in_stock < 1) {
                        $message = 'Sản phẩm đã hết hàng';
                        return $this->responseError($message, $product, 400, config('error_code.quantity_invalid'));
                    }
                    Cart::create([
                        'product_id' => $id,
                        'quantity' => 1,
                    ]);
                }
            

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error add product to cart', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function storeOrder(StoreOrderRequest $request)
    {
        try {
            $carts = Cart::with('product')->get()->toArray();
            $cartValue = $this->calculateCartValue($carts);
            if (count($carts) === 0) {
                $message = 'Bạn chưa thêm sản phẩm vào giỏ';
                return $this->responseError($message, [],400);
            }

            $products = $this->getProductQuantityInvalid($carts);
            if (count($products) > 0) {
                $message = 'Tồn kho của sản phẩm không đủ';
                return $this->responseError($message, $products, 400);
            }

            $order = Order::create([
                'customer_id' => $request->input('customer_id'),
                'code' => Order::generateCode(),
                'total_value' => $cartValue['total_value'],
                'total_original_price' => $cartValue['total_original_price'],          
                'note' => $request->input('note'),
                'delivery_address' => $request->input('delivery_address'),
                'status' => Order::STATUS['PENDING'],
                'creator_id' => auth()->id(),
                'receiver' => $request->input('receiver'),
                'receiver_phone' => $request->input('receiver_phone'),
                'transport_fee' => (int)$request->input('transport_fee')
            ]);
            
            OrderHistory::create([
                'order_id' => $order->_id,
                'user_id' => auth()->id(),
                'status' => Order::STATUS['PENDING']
            ]);
            $this->storeOrderItems($order->_id, $carts);
            $this->resetCart();
            return $this->responseSuccess();

        } catch (Exception $e) {
            Log::error('Error make order', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }   
    }

    public function updateCart(UpdateCartProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);
            $quantity = (int)$request->input('quantity');

            if (!$product) {
                $message = 'Sản phẩm không tồn tại';
                return $this->responseError($message, [], 400, config('error_code.not_found'));
            }

            if ($quantity > $product->quantity_in_stock) {
                $message = 'Tồn kho của sản phẩm không đủ';
                return $this->responseError($message, [$product], 400, config('error_code.quantity_invalid'));
            }
                $cart = Cart::where('product_id', $id)->first();
                if ($cart) {
                    $cart->quantity = $quantity;
                    $cart->save();
                }

            return $this->responseSuccess();

        } catch (Exception $e) {
            Log::error('Error update product in cart', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    private function calculateCartValue($carts) {
        $total = 0;
        $totalOriginalPrice = 0;
        if (!empty($carts)) {
            foreach ($carts as $item) {
                if (isset($item['product'])) {
                    $total += (int)$item['product']['sale_price'] * (int)$item['quantity'];
                    $totalOriginalPrice += (int)$item['product']['original_price'];
                }
            }
        }

        return [
            'total_value' => $total,
            'total_original_price' => $totalOriginalPrice
        ];
    }

    private function getProductQuantityInvalid($cartItems)
    {
        $products = [];
        foreach ($cartItems as $item) {
            if (isset($item['product'])) {
                $validQuantity = $item['quantity'] <= $item['product']['quantity_in_stock'];
                if (!$validQuantity) {
                    $products[] = $item['product'];
                }
            }
        }
        return $products;
    }

    private function storeOrderItems($orderId, $cartItems)
    {
        foreach ($cartItems as $item) {
            $total = 0;
            if (isset($item['product'])) {
                $product = $item['product'];
                $total += (int)$product['sale_price'] * (int)$item['quantity'];

                OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'sku' => $product['sku'],
                    'name' => $product['name'],
                    'image' => $product['image'],
                    'description' => $product['description'],
                    'weight' => (int)$product['weight'],
                    'sale_price' => (int)$product['sale_price'],
                    'original_price' => (int)$product['original_price'],
                    'quantity_in_stock' => (int)$product['quantity_in_stock'],
                    'quantity' => (int)$item['quantity'],
                    'total_value' => $total,
                ]);

                Product::where('_id', $item['product_id'])->update([
                    'quantity_in_stock' => (int)$product['quantity_in_stock'] - (int)$item['quantity']
                ]);
            }
        }
    }

    private function resetCart()
    {
        Cart::truncate();
    }

    public function destroy($id)
    {
        Cart::destroy($id);

        return $this->responseSuccess();
    }
}
