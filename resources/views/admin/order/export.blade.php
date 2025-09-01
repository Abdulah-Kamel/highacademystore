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
    </style>
</head>

<body dir="rtl">
    <h1>الطلبات الناجحة</h1>
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
            @foreach ($orders as $order)

                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->name }}</td>
                    <td>{{ $order->mobile }}</td>
                    <td>{{ optional($order->shipping)->name ?: '-' }}
                    </td>
                    <td>{{ $order->address }}</td>

                    <td>
                        {{ $order->orderDetails->sum('amout') }}
                    </td>
                    <td>
                        @foreach ($order->orderDetails as $d)
                            {{ $d->amout }} {{ $d->products->short_name ?? 'محذوف' }}<br>
                        @endforeach
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
