<?php

namespace App\Http\Controllers;

use App\Exports\ExportExcel;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Order\ChangeOrderStatusRequest;
use App\Models\OrderHistory;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    use ResponseTrait;
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->has('status') && in_array((int)$request->input('status'), Order::STATUS)) {
            $query->where('status', (int)$request->input('status'));
        }

        if ($request->has('q') && strlen($request->input('q')) > 0 ) {
            $query->where(function ($query) use($request) {
                $query ->Where('code', 'LIKE', "%" .  $request->input('q') . "%")
                    ->orWhere('receiver','LIKE',"%". $request->input('q') . "%");
            });                 
        }

        if ($request->has('start_time') && $request->has('end_time')) {
            $start = Carbon::createFromTimestamp($request->input('start_time'))->startOfDay();
            $end = Carbon::createFromTimestamp($request->input('end_time'))->endOfDay();
            $query = $query->whereBetween('created_at', [$start, $end]);
        }

        $orders = $query->orderBy('created_at', 'DESC')->with('customer')->paginate(config('constants.per_page'));

        return $this->responseSuccess($orders);
    }

    public function changeStatusOneOrder(ChangeOrderStatusRequest $request, $id)
    {
        try {
            $order = Order::find($id);
            $status = (int) $request->input('status');


            if ($order) {
                $success = false;
                switch ($status) {
                    case Order::STATUS['CANCELED']:
                        if ($order->status === Order::STATUS['PENDING'] || $order->status === Order::STATUS['APPROVED']) {
                            $success = $this->handleUpdateOrder($order, $status);
                            $this->returnProductQuantityInStock($order);
                        }
                        break;

                    case Order::STATUS['APPROVED']:
                        if ($order->status === Order::STATUS['PENDING']) {
                            $success = $this->handleUpdateOrder($order, $status);
                        }
                        break;

                    case Order::STATUS['DELIVERED']:
                        if ($order->status === Order::STATUS['APPROVED']) {
                            $success = $this->handleUpdateOrder($order, $status);
                        }
                        break;

                    case Order::STATUS['SHIPPED']:
                        if ($order->status === Order::STATUS['DELIVERED']) {
                            $success = $this->handleUpdateOrder($order, $status);
                        }
                        break;

                    case Order::STATUS['RETURNED']:
                        if ($order->status === Order::STATUS['DELIVERED'] || $order->status === Order::STATUS['SHIPPED']) {
                            $success = $this->handleUpdateOrder($order, $status);
                            $this->returnProductQuantityInStock($order);
                        }
                        break;

                }
                if (!$success) {
                    return $this->responseError('error', [], 400);
                }
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error change order status', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
     }

    private function returnProductQuantityInStock($order){
        $orderItems = OrderItem::where('order_id', $order->_id)->get();
        foreach ($orderItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->quantity_in_stock += $item->quantity;
                $product->save();
            };
        }
    }

    private function handleUpdateOrder($order, $status) {
        $order->status = $status;
        $order->save();
        OrderHistory::create([
            'order_id' => $order->_id,
            'user_id' => auth()->id(),
            'status' => $status
        ]);

        return true;
    }

    public function changeStatusOrders(Request $request)
    {
        try {
            $orderIds = $request->input('order_id');
            $status = (int)$request->input('status');
            foreach ($orderIds as $orderId) {
                $orders = Order::where('_id', $orderId)
                    ->where('status', '!=', Order::STATUS['CANCELED'])
                    ->where('status', '!=', Order::STATUS['RETURNED'])
                    ->get();

                if (count($orders) != 0) {
                    $orderArray = Order::where('_id', $orderId)->get();
                    if ($orderArray) {
                        foreach ($orderArray as $order) {
                            if ($order) {
                                $success = false;
                                switch ($status) {
                                    case Order::STATUS['CANCELED']:
                                        if ($order->status === Order::STATUS['PENDING'] || $order->status === Order::STATUS['APPROVED']) {
                                            $this->handleUpdateOrder($order, $status);
                                            $this->returnProductQuantityInStock($order);
                                        }
                                    case Order::STATUS['APPROVED']:
                                        if ($order->status === Order::STATUS['PENDING']) {
                                            $this->handleUpdateOrder($order, $status);
                                        }
                                        break;

                                    case Order::STATUS['DELIVERED']:
                                        if ($order->status === Order::STATUS['APPROVED']) {
                                            $this->handleUpdateOrder($order, $status);
                                        }
                                        break;

                                    case Order::STATUS['SHIPPED']:
                                        if ($order->status === Order::STATUS['DELIVERED']) {
                                            $this->handleUpdateOrder($order, $status);
                                        }
                                        break;

                                    case Order::STATUS['RETURNED']:
                                        if ($order->status === Order::STATUS['DELIVERED'] ||$order->status === Order::STATUS['SHIPPED']) {
                                            $this->handleUpdateOrder($order, $status);
                                            $this->returnProductQuantityInStock($order);
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }
            }
            return $this->responseSuccess();
        }
        catch (Exception $e) {
            Log::error('Error change orders status', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function show ($id){
        $order = Order::findOrFail($id)->load(['customer','items'])->load(array('histories' => function ($query) {
            $query->orderBy('created_at','DESC');
        }));
        return $this->responseSuccess($order);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function exportExcel(Request $request)
    {
        
        try {
            $query = Order::query();

            if ($request->has('q') && strlen($request->input('q')) > 0 ) {
                $query->where('code', 'LIKE', "%" . $request->input('q') . "%");
            }

            if ($request->has('status') && in_array((int)$request->input('status'), Order::STATUS)) {
                $query->where('status', (int)$request->input('status'));
            }

            if ($request->has('start_time') && $request->has('end_time')) {
                $start = Carbon::createFromTimestamp($request->input('start_time'))->startOfDay();
                $end = Carbon::createFromTimestamp($request->input('end_time'))->endOfDay();
                $query = $query->whereBetween('created_at', [$start, $end]);
            }
            
            $orders = $query->orderBy('created_at', 'DESC')->get();
            return Excel::download(new ExportExcel($orders), 'orders.xlsx');
           
        } catch (Exception $e) {
            Log::error('Error export order', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }
}
