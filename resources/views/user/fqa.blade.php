@extends('user.layouts.master')

@section('title')
    الاسئله الشائعة
@endsection


@section('content')
    <h1>الاسئلة الشائعة</h1>
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="accordion mt-5" id="accordionExample" dir="rtl" style="text-align: right;">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingnine">
                    <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapsenine" aria-expanded="true" aria-controls="collapsenine">
                        <span style="flex: 1; text-align: right;">
                            طرق الدفع على الموقع ايه ؟
                        </span>
                    </button>
                </h2>
                <div id="collapsenine" class="accordion-collapse collapse collapse " aria-labelledby="headingnine"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        الدفع عن طريق فوري باي
                        بعد ما تطلب الاوردر وتضغط على السلة وتضغط إتمام عملية الدفع

                        بتملي بياناتك وتضغط على علامة فوري ، هيجيلك كود مرجعي تدفع بيه من اي محل فيه ماكينة فوري
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingten">
                    <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseten" aria-expanded="true" aria-controls="collapseten">
                        <span style="flex: 1; text-align: right;">
                            لازم انزل ادفع بالكود على طول ؟
                        </span>
                    </button>
                </h2>
                <div id="collapseten" class="accordion-collapse collapse collapse " aria-labelledby="headingten"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        الكود صلاحيته ٤ ساعات فقط بعدها الطلب بتاعك بيتلغي تلقائيا
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingthree">
                    <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapsehgjhgjhgj" aria-expanded="true" aria-controls="collapsehgjhgjhgj">
                        <span style="flex: 1; text-align: right;">
                            مدة التوصيل قد ايه ؟
                        </span>
                    </button>
                </h2>
                <div id="collapsehgjhgjhgj" class="accordion-collapse collapse collapse " aria-labelledby="headingthree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        التوصيل بيكون خلال 3 ل 5 ايام عمل
                        مع العلم أن الجمعة والسبت إجازة رسمية لدي البريد المصري

                        متوسط وصول الشحنة (يومين) حسب المحافظة
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFFFF">
                    <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapsejhgjhgjghjgg" aria-expanded="true" aria-controls="collapsejhgjhgjghjgg">
                        <span style="flex: 1; text-align: right;">
                            ازاي اعرف ان الاوردر اتأكد بعد ما دفعت ؟
                        </span>
                    </button>
                </h2>
                <div id="collapsejhgjhgjghjgg" class="accordion-collapse collapse collapse " aria-labelledby="headingFFFF"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        بيجيلك ايميل أن تم الدفع بنجاح أو بتدخل على الويبسايت اضغط على التلت شرط اللي فوق وبعدها اختار طلباتي هتلاقي الطلب اتحول من طلب جديد لطلب ناجح
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingthree">
                    <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapsetjhgk" aria-expanded="true" aria-controls="collapsetjhgk">
                        <span style="flex: 1; text-align: right;">
                            هستلم الكتاب منين وازاي ؟
                        </span>
                    </button>
                </h2>
                <div id="collapsetjhgk" class="accordion-collapse collapse collapse " aria-labelledby="headingthree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        لو طلبت شحن عادى بيجيلك لحد اقرب مكتب بريد ودا حضرتك بتكتب اسمه فى البيانات بتاعتك عشان الكتب توصلك عليه ، أما لو اختارت شحن سريع بيكون التوصيل لحد باب البيت
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingJKHGJ">
                    <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapsetjhgjhgj" aria-expanded="true" aria-controls="collapsetjhgjhgj">
                        <span style="flex: 1; text-align: right;">
                            اعرف منين أن الكتب وصلت مكتب البريد ؟
                        </span>
                    </button>
                </h2>
                <div id="collapsetjhgjhgj" class="accordion-collapse collapse collapse " aria-labelledby="headingJKHGJ"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        احنا لما بنشحن الكتب بيجيلك ايميل من خلاله بتعمل تتبع للشحنة عن طريق باركود بنبعتهولك فى الايميل دا  بتنسخه وتضغط على تتبع الشحنة اللي موجودة ف الايميل فا بيحولك على صفحة البريد المصري وتلصق الباركود فى المكان المخصص وبعدها تضغط تتبع الشحنة فا بيجيلك مكان شحنتك بالضبط ، لما بتوصل مكتب البريد اللي كتبت اسمه وانت بتملي الاوردر هما هيتصلوا بيك عشان تستلم أو حضرتك بتروح ببطاقتك الشخصية وتقولهم انك ليك اوردر وعايز تستلمه                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingseven">
                    <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseseven" aria-expanded="true" aria-controls="collapseseven">
                        <span style="flex: 1; text-align: right;">
                            لو فيه مشكلة ف الكتاب وعايز استرد فلوسي ؟
                        </span>
                    </button>
                </h2>
                <div id="collapseseven" class="accordion-collapse collapse collapse " aria-labelledby="headingseven"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        حضرتك بتتواصل معانا وبنوجهك لمسئول الحسابات وهتسترد المبلغ عادي
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingeight">
                    <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseeight" aria-expanded="true" aria-controls="collapseeight">
                        <span style="flex: 1; text-align: right;">
                            هل الكتب اصلية ؟
                        </span>
                    </button>
                </h2>
                <div id="collapseeight" class="accordion-collapse collapse collapse " aria-labelledby="headingeight"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        ايوه يفندم اصلية لأننا معتمدين لدي كل المدرسين الاونلاين
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
