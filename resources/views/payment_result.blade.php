<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful Message Example</title>

    <meta name="author" content="Codeconvey" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background: #eee9e9;
        }

        #card {
            position: relative;
            width: 90%;
            max-width: 520px;
            height: 100vh;
            display: block;
            margin: 40px auto;
            text-align: center;
            font-family: 'Source Sans Pro', sans-serif;
        }

        #upper-side {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2em;
            color: #fff;
            border-top-right-radius: 8px;
            border-top-left-radius: 8px;
        }

        #upper-side i {
            font-size: 150px;
        }

        #checkmark {
            font-weight: lighter;
            fill: #fff;
            margin: auto;
            width: 100px;
            height: 100px;
        }

        #status {
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 50px;
            margin-top: 0.5em;
            margin-bottom: 0;
        }

        #lower-side {
            padding: 2em 2em 5em 2em;
            background: #fff;
            display: block;
            border-bottom-right-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        #message {
            margin-top: -.5em;
            color: #757575;
            letter-spacing: 1px;
            font-size: 1.5em;
        }

        #contBtn {
            font-size: 30px;
            position: relative;
            top: 1.5em;
            text-decoration: none;
            color: #fff;
            margin: auto;
            padding: .5em 3em;
            box-shadow: 0px 15px 30px rgba(50, 50, 50, 0.21);
            border-radius: 25px;
            transition: all .4s ease;
        }

        #contBtn:hover {
            box-shadow: 0px 15px 30px rgba(50, 50, 50, 0.41);
            transition: all .4s ease;
        }

        .success {
            background-color: #8BC34A;
        }

        .fail {
            background-color: #ee1010;
        }

        .pend {
            background-color: #eeca2a;
        }
    </style>
</head>

<body>

    @if($state == "success")
    <section>
        <div class="rt-container">
            <div class="col-rt-12">
                <div class="Scriptcontent">
                    <div id='card' class="animated fadeIn">
                        <div id='upper-side' class="success">

                            <i class="fa-solid fa-circle-check"></i>
                            <h1 id='status'>
                                عملية ناجحة
                            </h1>
                        </div>
                        <div id='lower-side'>
                            <h2 id='message'>
                                تم الدفع بنجاح يمكنك العودة الان الي الموقع وستجد العملية في قسم طلباتي
                            </h2>
                            <a href="{{ route('user.orders.user') }}" id="contBtn" class="success">استمرار</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if($state == "fail")
    <section>
        <div class="rt-container">
            <div class="col-rt-12">
                <div class="Scriptcontent">
                    <div id='card' class="animated fadeIn">
                        <div id='upper-side' class="fail">

                            <i class="fa-solid fa-circle-exclamation"></i>
                            <h1 id='status'>
                                عملية فاشلة
                            </h1>
                        </div>
                        <div id='lower-side'>
                            <h2 id='message'>
                                تم رفض عملية الدفع من جانب المزود اذا كنت تعتقد غير ذلك برجاء التواصل معنا
                            </h2>
                            <a href="{{ route('user.orders.user') }}" id="contBtn" class="fail">استمرار</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if($state == "pend")
    <section>
        <div class="rt-container">
            <div class="col-rt-12">
                <div class="Scriptcontent">
                    <div id='card' class="animated fadeIn">
                        <div id='upper-side' class="pend">

                            <i class="fa-solid fa-circle-exclamation"></i>
                            <h1 id='status'>
                                العملية قيد الدفع
                            </h1>
                        </div>
                        <div id='lower-side'>
                            <h2 id='message'>
                                العملية قيد الدفع توجه لاقرب فرع فوري او اي منفذ لدفع الكود
                            </h2>
                            <h2>{{$ref}}</h2>
                            <a href="{{ route('user.orders.user') }}" id="contBtn" class="pend">استمرار</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    
     @if($state == "coupon-pend")
    <section>
        <div class="rt-container">
            <div class="col-rt-12">
                <div class="Scriptcontent">
                    <div id='card' class="animated fadeIn">
                        <div id='upper-side' class="pend">

                            <i class="fa-solid fa-circle-exclamation"></i>
                            <h1 id='status'>
                                العملية قيد الدفع
                            </h1>
                        </div>
                        <div id='lower-side'>
                            <h2 id='message'>
                               ستظهر الكوبونات في صفحة كوبوناتي بعد اتمام الدفع بدقيقتين
                            </h2>
                            <a href="{{ route('user.vochers.user') }}" id="contBtn" class="pend">كوبوناتي</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

</body>

</html>