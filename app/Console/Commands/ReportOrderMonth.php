<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\ReportOrderMonth as ReportOrderMonthModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class ReportOrderMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:order-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report order monthly';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $yesterday = now()->subDays(1);
            $start = $yesterday->startOfMonth()->toDateTime();
            $end = $yesterday->endOfMonth()->toDateTime();
            $orderValue = Order::where('status', Order::STATUS['SHIPPED'])->whereBetween('created_at', [$start, $end])->sum('total_value');
            $reportMonthly = ReportOrderMonthModel::where('month', $yesterday->month)
                ->where('year', $yesterday->year)
                ->first();
            if (empty($reportMonthly)) {
                ReportOrderMonthModel::create([
                'total_value' => (int)$orderValue,
                'date' => $yesterday->toDateString(),
                'month' => $yesterday->month,
                'year' => $yesterday->year,
            ]);
            } else {
                $reportMonthly->date = $yesterday->toDateString();
                $reportMonthly->total_value = (int) $orderValue;
                $reportMonthly->save();
            }
        } catch (\Exception $exception) {
            Log::error('Error report order month', [
                'method' => __METHOD__,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
