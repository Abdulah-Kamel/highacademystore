<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public $limit;
    public function __construct($limit = 10)
    {
        $this->limit = $limit;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Order::with([
            'orderDetails.products',
            'shipping'      
        ])
            ->where('status', 'success')
            ->latest()
            ->limit($this->limit)
            ->get();
    }
    /**
     * Define the headings for the export.
     */
    public function headings(): array
    {
        return [
            'رقم الطلب',
            'الاسم',
            'الهاتف',
            'العنوان الأساسي',
            'نوع الشحن',
            'العنوان',
            'الكتب المطلوبة',
        ];
    }
    

    /**
     * Map the data for each user, including the custom column.
     */
    public function map($order): array
    {
        // build your books string...
        $books = $order->orderDetails
            ->map(fn($d) => $d->products
                ? "x{$d->amout} {$d->products->short_name}"
                : 'منتج محذوف')
            ->implode(', ');
    
        return [
            $order->id,
            $order->name,
            $order->mobile,
            optional($order->shipping)->name      ?: '-',
            $order->address,
            $books,
        ];
    }
    
}
