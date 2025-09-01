<?php
use ArPHP\I18N\Arabic;
$Arabic = new Arabic();
function reverseWords($string)
{
    // Split the string into an array of words
    $words = explode(' ', $string);

    // Reverse the array of words
    $reversedWords = array_reverse($words);

    // Join the reversed array back into a string
    return implode(' ', $reversedWords);
}

$grouped = $orders->groupBy(fn($order) => $order->shipping->name ?? $order->shipping_method);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <style type="text/css" media="all">
        >body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 14px;
            word-wrap: break-word;
            white-space: normal;
            padding: 0;
            margin: 0;
        }


        .export {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
        }

        .export,
        .export th,
        .export td {
            border: 1px solid black;
        }

        .export th,
        .export td {
            padding: 8px;
            text-align: right;
            word-break: break-word;
            white-space: normal;
        }

        .success {
            margin: 0;
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
            padding: 10px;
        }

        .cell {
            padding: 10px;
            vertical-align: top;
            /* Aligns content to the top */
        }

        .cell1 {
            width: 70%;
        }

        .cell3 {
            width: 30%;
            border-right: 1px solid #16404D;
            height: fit-content;
        }

        .table-bordered,
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #16404D;
        }

        .success th,
        .success td {
            padding: 8px;
            text-align: right;
            word-break: break-word;
            white-space: normal;
        }

        .page {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
            page-break-after: always;
        }
    </style>
</head>

<body dir="rtl">
    {{-- <h1>الطلبات الناجحة</h1> --}}
    @php
        $ordersForShipping = collect($grouped)
            ->only(['شحن لاقرب مكتب بريد', 'شحن لباب البيت'])
            ->collapse();
    @endphp

    @foreach ($ordersForShipping->chunk(2) as $chunkIndex => $twoOrders)
        <!-- Group orders in chunks of 2 -->
        <div class="page" style="padding: 0; margin: 0;">
            @foreach ($twoOrders as $order)
                @php
                    // dd($order);
                    $parts = explode(' - ', $order->address);
                    $governorate = $parts[0] ?? '';
                    $city = $parts[1] ?? '';
                @endphp
                <div style="border: 1px dashed #1c2b30; padding: 5px; margin: 0px 0 20px 0;">
                    <p style="text-align: center; font-size: 26px; font-weight: 900; margin-bottom: 0px; margin-top: 0px;">
                        High Academy Store</p>

                    <table class="success">
                        <tr>
                            <td class="cell cell1" style="font-size: 18px;">
                                <h2 style="color: #1c2b30; font-size:20px; margin: 0px;">
                                    المرسل إليه
                                </h2>

                                <br>
                                <p style="margin: 0px;"><b style="color: #1c2b30;">الاسم</b>:
                                    {{ $order->name ?? $order->user->name }}
                                </p>
                                <br />
                                <p><b>المحافظه</b>: {{ $governorate }}</p>
                                <p><b>المدينة / المركز</b>: {{ $city }}</p>
                                <br />
                                <p style="margin: 0px;"><b style="color: #1c2b30;">اسم مكتب
                                        البريد</b>: {{ $order->near_post ? $order->near_post : '' }}</p>
                                <br />
                                <p style="margin: 0px;"><b style="color: #1c2b30;">العنوان
                                        بالتفصيل</b>: {{ $order->address }}
                                    <br /> {{ $order->address2 ?? $order->users->address }}
                                </p>
                                <br />
                                <p style="margin: 0px; text-align: center;"><b style="color: #1c2b30;">رقم الموبايل</b>:
                                    0{{ $order->mobile }}</p>
                                <br />
                                <p style="margin: 0px;"><b style="color: #1c2b30;">رقم
                                        احتياطي</b>: {{ $order->temp_mobile ?? $order->user->phone }}</p>
                            </td>
                            <td class="cell cell3">
                                <h2 style="color: #118B50; font-size:20px; margin: 0px;">الراسل</h2>
                                <br>
                                <p style="font-size: 14px; color: #118B50; margin: 0px;"><b style="color: #118B50;">الاسم</b>:
                                    مكتبة يُسْر عنهم</p>
                                <br />
                                <p style="font-size: 14px; color: #118B50; margin: 0px;"><b style="color: #118B50;">المهندس</b>:
                                    احمد علام</p>
                                <br />
                                <p style="font-size: 14px; color: #118B50; margin: 0px;"><b style="color: #118B50;">رقم
                                        الموبيل</b>: 01060683708</p>
                                <br />
                                <p style="font-size: 14px; color: #118B50; margin: 0px;"><b style="color: #118B50;">العنوان</b>:
                                    المنوفية - شبين الكوم <br> امام نادى
                                    التجارة
                                </p>
                                <br />
                                @if($order->shipping_method == 2)
                                    <p><b>نوع الشحن</b>:
                                        <span style="color: red;">البريد السريع</span>
                                    </p>
                                @else
                                    <p><b>نوع الشحن</b>:
                                        <span>{{ optional($order->shipping)->name }}</span>
                                    </p>
                                @endif

                                <br />
                                <p style="font-size: 14px; color: #118B50; margin: 0px;"><b style="color: #118B50;">رقم
                                        الطلب</b>: {{ $order->id }}</p>
                                <br />
                            </td>
                        </tr>
                    </table>
                    <table class="success table-bordered" style="margin-top: 5px;">
                        <thead>
                            <tr>
                                <th style="color: #16404D; text-style: bold;">{{ 'الكتب المطلوبة' }}</th>
                                @foreach ($order->orderDetails as $detail)
                                    <td style="color: #16404D;">
                                        {{ $detail->products->short_name ?? $detail->products->name }}
                                        {{ @$detail->color ?? ' ' }} {{ ' ' . @$detail->size ?? ' ' }}
                                    </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="color: #16404D; text-style: bold;">{{ 'العدد المطلوب' }}</th>
                                @foreach ($order->orderDetails as $detail)
                                    <td style="color: #16404D;">{{ $detail->amout }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

    @endforeach

    @foreach (collect($grouped)->except(['شحن لاقرب مكتب بريد', 'شحن لباب البيت']) as $shipping => $o)
        <h2>{{ $shipping }}</h2>
        <table class="export">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>الاسم</th>
                    <th>الهاتف</th>
                    <th>نوع الشحن</th>
                    <th>العنوان</th>
                    <th>عدد الكتب</th>
                    <th>الكتب المطلوبة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($o as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->mobile }}</td>
                        <td>{{ ($order->shipping_method == 2) ? 'البريد السريع' : (optional($order->shipping)->name ?: $order->shipping_method) }}
                        </td>
                        <td>{{ $order->address }}</td>

                        <td>
                            {{ $order->orderDetails->sum('amout') }}
                        </td>
                        <td>
                            @foreach ($order->orderDetails as $d)
                                x{{ $d->amout }} {{ $d->products->short_name ?? 'محذوف' }}<br>
                            @endforeach
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    @endforeach
    <p>
        @foreach ($grouped as $shipping => $o)
            <b>{{ $shipping }}</b> ({{ $o->count() }})
            <br>
        @endforeach
    </p>
    <h2>الكتب المطلوبة</h2>
    @php
        $allDetails = collect($orders)->flatMap(function ($order) {
            return $order->orderDetails;
        });
        $mergedBooks = $allDetails->groupBy(fn($detail) => $detail->products->id)
            ->map(function ($group) {
                return [
                    'name' => $group->first()->products->short_name ?? $group->first()->products->name,
                    'total' => $group->sum('amout'),
                ];
            });
    @endphp
    <table class="export">
        <thead>
            <tr>
                <th>اسم الكتاب</th>
                <th>الكمية</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mergedBooks as $book)
                <tr>
                    <td>{{ $book['name'] }}</td>
                    <td>{{ $book['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
