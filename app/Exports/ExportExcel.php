<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportExcel implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->order;
    }
    /**
     * Set header columns
     *
     * @return array
     */
    public function headings() :array
    {
        return [
            'Mã đơn hàng',
            'Tên người nhận',
            'Tổng tiền',
            'Trạng thái',
            'Địa chỉ nhận hàng',
            'Ngày tạo đơn',
        ];
    }
     /**
     * Mapping data
     *
     * @param $order
     * @return array
     */
    public function map($order) :array
    {
        return [
            $order->code,
            $order->receiver,
            number_format((int)$order->total_value, 0, '', ','),
            $this->getStatusNameOnExport($order),
            $order->delivery_address,
            Carbon::parse($order->created_at)->format('H:i d/m/Y'),
        ];
    }

    /**
     * @param $order
     * @return string
     */
    private function getStatusNameOnExport($order) {
        $status = '';

        switch ($order->status) {
            case Order::STATUS['PENDING']:
                $status = 'Chờ xử lý';
                break;
            case Order::STATUS['APPROVED']:
                $status = 'Đã xử lý';
                break;
            case Order::STATUS['DELIVERED']:
                $status = 'Đang giao';
                break;
            case Order::STATUS['SHIPPED']:
                $status = 'Đã giao';
                break;
            case Order::STATUS['CANCELED']:
                $status = 'Đã hủy';
                break;
            case Order::STATUS['RETURNED']:
                $status = 'Trả hàng';
                break;
        }

        return $status;
    }
}
