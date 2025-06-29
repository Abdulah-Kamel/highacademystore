@extends('user.layouts.master')

@section('title')
    تواصل معنا
@endsection

@include('user.layouts.css')
@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="{{ route('user.home') }}">Home</a>
                    <span class="breadcrumb-item active">Contact</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Contact Start -->
    <div class="container-fluid mt-5">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3"></span> منصات
            التواصل</h2>
        <div class="row px-xl-5">
            <div class="col-lg-6">
                <div class=" mb-4">
                    <a href="https://www.facebook.com/share/167NUs1tSx/"
                        class="d-flex flex-column align-items-center justify-content-center text-decoration-none text-center"
                        target="_blank">
                        <img src="{{ asset('storage/images/—Pngtree—facebook social media icon_3572498.png') }}" alt="facebook logo" height="200px">
                        <p class="text-black">تابع الصفحه من هنا
                        </p>
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class=" mb-4">
                    <a href="https://www.facebook.com/groups/1682649475931814/?ref=share&mibextid=NSMWBT"
                        class="d-flex flex-column align-items-center justify-content-center text-decoration-none text-center"
                        target="_blank">
                         <img src="{{ asset('storage/images/—Pngtree—facebook social media icon_3572498.png') }}" alt="facebook logo" height="200px">
                        <p class="text-black">انضم الى جروبنا على الفيسبوك
                            <br>
                            عشان تعرف مواعيد نزول الكتب الجديده
                        </p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->
    <!-- Contact Start -->
    <!--<div class="container-fluid mt-5">-->
    <!--    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">تواصل معنا-->
    <!--            عن طريق الميل </span></h2>-->
    <!--    <div class="row px-xl-5">-->
    <!--        <div class="col-lg-7 mb-5">-->
    <!--            <div class="contact-form bg-light p-30">-->
    <!--                <div id="success"></div>-->
    <!--                <form name="sentMessage" id="contactForm" novalidate="novalidate">-->
    <!--                    <div class="control-group">-->
    <!--                        <input type="text" class="form-control" id="name" placeholder="Your Name"-->
    <!--                            required="required" data-validation-required-message="Please enter your name" />-->
    <!--                        <p class="help-block text-danger"></p>-->
    <!--                    </div>-->
    <!--                    <div class="control-group">-->
    <!--                        <input type="email" class="form-control" id="email" placeholder="Your Email"-->
    <!--                            required="required" data-validation-required-message="Please enter your email" />-->
    <!--                        <p class="help-block text-danger"></p>-->
    <!--                    </div>-->
    <!--                    <div class="control-group">-->
    <!--                        <input type="text" class="form-control" id="subject" placeholder="Subject"-->
    <!--                            required="required" data-validation-required-message="Please enter a subject" />-->
    <!--                        <p class="help-block text-danger"></p>-->
    <!--                    </div>-->
    <!--                    <div class="control-group">-->
    <!--                        <textarea class="form-control" rows="8" id="message" placeholder="Message" required="required"-->
    <!--                            data-validation-required-message="Please enter your message"></textarea>-->
    <!--                        <p class="help-block text-danger"></p>-->
    <!--                    </div>-->
    <!--                    <div>-->
    <!--                        <button class="btn btn-primary py-2 px-4" type="submit" id="sendMessageButton">-->
    <!--                            ارسل الرساله</button>-->
    <!--                    </div>-->
    <!--                </form>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        <div class="col-lg-5 mb-5">-->
    <!--            <div class="bg-light p-30 mb-30">-->
    <!--                <iframe style="width: 100%; height: 250px;"-->
    <!--                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3001156.4288297426!2d-78.01371936852176!3d42.72876761954724!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4ccc4bf0f123a5a9%3A0xddcfc6c1de189567!2sNew%20York%2C%20USA!5e0!3m2!1sen!2sbd!4v1603794290143!5m2!1sen!2sbd"-->
    <!--                    frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>-->
    <!--            </div>-->
    <!--            <div class="bg-light p-30 mb-3">-->
    <!--                <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i> شبين الكوم المنوفيه </p>-->
    <!--                <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>ahmedallam90247@gmail.com</p>-->
    <!--                <p class="mb-2"><i class="fa fa-phone-alt text-primary mr-3"></i>01060643708</p>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    <!-- Contact End -->
@endsection
<script src="https://kit.fontawesome.com/81c1afbf5a.js" crossorigin="anonymous"></script>
