<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReportOrderDaily;
use App\Models\ReportOrderMonth;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class ReportController extends Controller
{
    use ResponseTrait;
    public function index()
    {   
        $query = Order::where('status', Order::STATUS['SHIPPED']);
        $completedOrders = $query->count();
        $orderWaiting = Order::where('status', Order::STATUS['PENDING'])->count();
        $totalMoneys = (int) $query->sum('total_value');
        $totalOriginalPrice = (int) $query->sum('total_original_price');
        $transportFee = (int) $query->sum('transport_fee');
        $totalProfit = $totalMoneys - ($totalOriginalPrice + $transportFee);

        return $this->responseSuccess([
            'completedOrders' => $completedOrders,
            'orderWaiting' => $orderWaiting,
            'totalMoneys' => $totalMoneys,
            'totalProfit' => $totalProfit
        ]);
    }

    public function getDataOrder()
    {
        $reports = ReportOrderDaily::query()
            ->where('date', '>', now()->subDays(7)->toDateString())->orderBy('created_at','ASC')
            ->get();

        $data = [
            'labels' => $reports->pluck('label')->toArray(),
            'data' => $reports->pluck('total_value')->toArray(),
        ];

        return $this->responseSuccess($data);
    }

    public function getDataOrderMonth()
    {
        $reports = ReportOrderMonth::query()
            ->where('date', '>', now()->subMonths(5)->startOfMonth()->toDateString())->orderBy('created_at','ASC')
            ->get();
        $data = [
            'labels' => $reports->pluck('label')->toArray(),
            'data' => $reports->pluck('total_value')->toArray(),
        ];

        return $this->responseSuccess($data);
    }
}
