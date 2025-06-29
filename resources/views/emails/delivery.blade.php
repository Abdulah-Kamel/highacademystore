<!DOCTYPE html>
<html lang="ar">
    <head>
        <meta charset="UTF-8">
        <title>تم شحن المنتج بنجاح</title>
        <style>
            body {
                background-color: #f3f4f8;
                font-size: 18px;
                margin: 20px;
                width: 80%;
                margin: 0 auto;
                font-family: sans-serif;
            }
            p {
                background-color: #fff;
                padding: 15px 20px;
                border-radius: 15px;
            }
            a {
                
            }
            h1 {
                font-size: 30px;
                font-weight: bold;
                margin-bottom: 20px;
                text-align: center;
            }
            h6 {
                direction: ltr;
            }
            span {
                font-size: small; 
                display: block;
                margin-top: 5px;
            }
        </style>
    </head>
    <body dir="rtl">
        <h1>عميلنا العزيز , تم شحن الاوردر الخاص بك بنجاح</h1>
        <div>
            <p>الشحنة بتاخد من يومين الى خمس ايام عمل عشان توصلك سواء شحنت لأقرب مكتب بريد أو لحد البيت</p>
            <p>
                لمتابعة شحنتك حتى وصولها مكتب البريد 
                انسخ البار كود دا {{ $details['barcode'] }}
            </p>
            <a style="background-color: #007bff; color: white; padding: 15px 20px; border-radius: 10px; text-decoration: none; display: block;text-align: center;" href="https://egyptpost.gov.eg/ar-eg//Home/EServices/Track-And-Trace">تتبع شحنتك</a> 
            <p>
                لو محتاج تستفسر عن اسئلة تانية تقدر تشوف صفحة الاسئلة الشائعة <a style="color: #007bff; text-decoration: none;font-weight: bold;" href="{{ env('APP_URL') . "/ar/fqa"}}">من هنا</a>
            </p>
            <h6>High Academy Store</h6>
        </div>
    </body>
</html>