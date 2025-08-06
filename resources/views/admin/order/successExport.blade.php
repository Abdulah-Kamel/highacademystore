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


        table {
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

        th,
        td {
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
        }
    </style>
</head>


<body dir="rtl">
    @foreach ($orders->chunk(2) as $orderChunk)
        <!-- Group orders in chunks of 2 -->
        <div class="page" style="padding: 0; margin: 0;">
            @foreach ($orderChunk as $order)
                @php
                    $parts = explode(' - ', $order->address);
                    $governorate = $parts[0] ?? '';
                    $city = $parts[1] ?? '';
                @endphp
                <div style="border: 1px dashed #1c2b30; padding: 5px; margin: 0px 0 20px 0;">
                    <p
                        style="text-align: center; font-size: 26px; font-weight: 900; margin-bottom: 0px; margin-top: 0px;">
                        High Academy Store</p>

                    <table>
                        <tr>
                            <td class="cell cell1" style="font-size: 18px;">
                                <h2 style="color: #1c2b30; font-size:20px; margin: 0px;">
                                    المرسل إليه
                                </h2>

                                <br>
                                <p style="margin: 0px;"><b style="color: #1c2b30;">الاسم</b>:
                                    {{ $order->name ?? $order->user->name }}</p>
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
                                <p style="font-size: 14px; color: #118B50; margin: 0px;"><b
                                        style="color: #118B50;">الاسم</b>: مكتبة يُسْر عنهم</p>
                                <br />
                                <p style="font-size: 14px; color: #118B50; margin: 0px;"><b
                                        style="color: #118B50;">المهندس</b>: احمد علام</p>
                                <br />
                                <p style="font-size: 14px; color: #118B50; margin: 0px;"><b style="color: #118B50;">رقم
                                        الموبيل</b>: 01060683708</p>
                                <br />
                                <p style="font-size: 14px; color: #118B50; margin: 0px;"><b
                                        style="color: #118B50;">العنوان</b>: المنوفية - شبين الكوم <br> امام نادى
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
                                @if ($order->is_fastDelivery == 1)
                                    <p style="text-align: left; font-weight: 900; font-size: 24px; color: red;">
                                        <b>
                                            البريد السريع
                                        </b>
                                    </p>
                                @endif
                            </td>
                        </tr>
                    </table>
                    <table class="table-bordered" style="margin-top: 5px;">
                        <thead>
                            <tr>
                                <th style="color: #16404D; text-style: bold;">{{ 'الكتب المطلوبة' }}</th>
                                @foreach ($order->orderDetails as $detail)
                                    <td style="color: #16404D;">
                                        {{ $detail->products->short_name ?? $detail->products->name }}
                                        {{ @$detail->color ?? ' ' }} {{ ' ' . @$detail->size ?? ' ' }}</td>
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
                            {{--                    @php --}}
                            {{--                        $hasColor = false; --}}
                            {{--                        $hasSize = false; --}}

                            {{--                        foreach ($order->orderDetails as $item) { --}}
                            {{--                            // تحقق إذا كان اللون ليس null --}}
                            {{--                            if ($item->color !== null) $hasColor = true; --}}
                            {{--                            // تحقق إذا كان الحجم ليس null --}}
                            {{--                            if ($item->size !== null) $hasSize = true; --}}
                            {{--                        } --}}
                            {{--                    @endphp --}}


                            {{--                    @if ($hasColor) --}}
                            {{--                        <tr> --}}
                            {{--                            <th style="color: #16404D; font-weight: bold;">اللون</th> --}}
                            {{--                            @foreach ($order->orderDetails as $detail) --}}
                            {{--                                <td style="color: #16404D;">{{ $detail->color ?? '-' }}</td> --}}
                            {{--                            @endforeach --}}
                            {{--                        </tr> --}}
                            {{--                    @endif --}}

                            {{--                    @if ($hasSize) --}}
                            {{--                        <tr> --}}
                            {{--                            <th style="color: #16404D; font-weight: bold;">الحجم</th> --}}
                            {{--                            @foreach ($order->orderDetails as $detail) --}}
                            {{--                                <td style="color: #16404D;">{{ $detail->size ?? '-' }}</td> --}}
                            {{--                            @endforeach --}}
                            {{--                        </tr> --}}
                            {{--                    @endif --}}


                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
        @if (!$loop->last)
            <div style="page-break-after: always;"></div> <!-- Add a page break after every chunk -->
        @endif
    @endforeach

</body>

</html>
