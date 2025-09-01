{{-- resources/views/emails/success_paid.blade.php --}}
@php use Illuminate\Support\Str; @endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم دفع طلبك بنجاح</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background-color: #f4f4f4;
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            direction: rtl;
            /* Enforce RTL layout */
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #dddddd;
        }

        .header {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }

        .content h2 {
            color: #2c3e50;
            font-size: 20px;
        }

        .order-summary-table,
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-summary-table th,
        .order-summary-table td,
        .totals-table td {
            padding: 12px;
            border-bottom: 1px solid #eeeeee;
            text-align: right;
        }

        .order-summary-table th {
            background-color: #f9f9f9;
            font-weight: bold;
            color: #555555;
        }

        .shipping-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            border: 1px solid #eeeeee;
        }

        .shipping-details p {
            margin: 5px 0;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }

        .button {
            background-color: #3498db;
            color: #ffffff !important;
            /* Important for email client compatibility */
            padding: 15px 25px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777777;
            background-color: #f4f4f4;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>High Academy Store</h1>
        </div>
        <div class="content">
            <h2>أهلاً {{ $order->name }},</h2>
            @if($order->status != 'reserved')
            <h2>طلبك قيد التجهيز!</h2>
            @endif
            <p>شكراً لطلبك من متجرنا. طلبك رقم <strong>#{{ $order->id }}</strong> قد تم تأكيده .</p>

            {{-- @if (!Str::contains(optional($order->shipping)->name, 'استلام من المكتبة') || optional($order->status)->isNot('reserved'))
                <div class="shipping-details">
                    <p>سيصل إليك الطلب خلال <strong>3–5 أيام عمل</strong>.
                        <br><small>(يرجى العلم أن الجمعة والسبت إجازة في شركات الشحن).</small>
                    </p>
                </div>
            @endif --}}

            <h3>ملخص الطلب</h3>
            <table class="order-summary-table">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderDetails as $detail)
                        <tr>
                            <td>
                                {{ $detail->products->name ?? 'منتج محذوف' }}
                                @if ($detail->color || $detail->size)
                                    <br><small>
                                        @if ($detail->color)
                                            اللون: {{ $detail->color }}
                                        @endif
                                        @if ($detail->size)
                                            الحجم: {{ $detail->size }}
                                        @endif
                                    </small>
                                @endif
                            </td>
                            <td>{{ $detail->amout }}</td>
                            <td>{{ number_format($detail->price * $detail->amout, 2) }} جنيه</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="totals-table">
                <tbody>
                    <tr>
                        <td>قيمة الكتب</td>
                        <td style="text-align:right;">{{ number_format($order->amount, 2) }} جنيه</td>
                    </tr>
                    <tr>
                        <td>رسوم الشحن</td>
                        <td style="text-align:right;">{{ number_format($order->delivery_fee, 2) }} جنيه</td>
                    </tr>
                    <tr>
                        <td>رسوم الخدمة</td>
                        <td style="text-align:right;">{{ number_format($order->service_fee, 2) }} جنيه</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; font-size: 18px;">الإجمالي المدفوع</td>
                        <td style="text-align:right; font-weight:bold; font-size: 18px;">
                            {{ number_format($order->total, 2) }} جنيه</td>
                    </tr>
                </tbody>
            </table>

            <div class="shipping-details">
                <h4>تفاصيل الشحن</h4>
                <p><strong>طريقة الشحن:</strong> {{ optional($order->shipping)->name ?: '-' }}</p>
                @if (optional($order->shipping)->address)
                    <p><strong>عنوان الشحن:</strong> {{ $order->shipping->address }}</p>
                @endif
                @if (optional($order->shipping)->phones)
                    <p><strong>أرقام التواصل:</strong> {{ implode(', ', $order->shipping->phones) }}</p>
                @endif
                @if ($order->near_post)
                    <p><strong>أقرب مكتب بريد:</strong> {{ $order->near_post }}</p>
                @endif
            </div>


            <div class="button-container">
                <a href="{{ url('/ar/myorders/' . $order->id) }}" class="button">عرض تفاصيل الطلب</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} High Academy Store. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>

</html>
