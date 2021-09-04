<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\ReportOrderDaily as ReportOrderDailyModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReportOrderDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:order-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report order daily';

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
            $start = $yesterday->startOfDay()->toDateTime();
            $end = $yesterday->endOfDay()->toDateTime();
            $orderValue = Order::where('status', Order::STATUS['SHIPPED'])->whereBetween('created_at', [$start, $end])->sum('total_value');
            $reportDaily = ReportOrderDailyModel::where('date', $yesterday->toDateString())->first();
            if (empty($reportDaily)) {
                ReportOrderDailyModel::create([
                    'date' => $yesterday->toDateString(),
                    'total_value' => (int) $orderValue,
                ]);
            } else {
                $reportDaily->total_value = (int) $orderValue;
                $reportDaily->save();
            }
        } catch (\Exception $exception) {
            Log::error('Error report order daily', [
                'method' => __METHOD__,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
