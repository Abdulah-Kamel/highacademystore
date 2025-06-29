<!DOCTYPE html>

<html lang="ar" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact " dir="rtl"
      data-theme="theme-default" data-assets-path="{{asset('Admin')}}/vuexy-html-admin-template/assets/"
      data-template="vertical-menu-template">

<head>
    <title>لوحة التحكم | @yield('title')</title>
    @include('Admin.includes.meta')
    <!-- Canonical SEO -->
    @include('Admin.includes.css')
    <link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/select2/select2.css"/>
</head>

<body>


<!-- ?PROD Only: Google Tag Manager (noscript) (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5DDHKGP" height="0" width="0"
            style="display: none; visibility: hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar  ">
    <div class="layout-container">


        <!-- Menu -->

        @include('Admin.layout.sidebar')
        <!-- / Menu -->


        <!-- Layout container -->
        <div class="layout-page">


            <!-- Navbar -->

            @include('Admin.layout.header')

            <!-- / Navbar -->


            <!-- Content wrapper -->
            <div class="content-wrapper">

                <!-- Content -->

                <div class="container-xxl flex-grow-1 container-p-y">
                    @yield('content')
                    <div class="modal-onboarding modal fade animate__animated" id="onboardHorizontalImageModal"
                         tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content text-center">
                                <div class="modal-header border-0">
                                    <a class="text-muted close-label" href="javascript:void(0);"
                                       data-bs-dismiss="modal">تخطي</a>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <div class="modal-body onboarding-horizontal p-0">
                                    <div class="onboarding-media">
                                        <img
                                            src="{{asset('Admin/vuexy-html-admin-template')}}/assets/img/illustrations/boy-verify-email-light.png"
                                            alt="boy-verify-email-light" width="273" class="img-fluid"
                                            data-app-light-img="illustrations/boy-verify-email-light.png"
                                            data-app-dark-img="illustrations/boy-verify-email-dark.png">
                                    </div>
                                    <div class="onboarding-content mb-0 body">

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel1"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body bodyModel">

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->


                <!-- Footer -->
                @include('Admin.layout.footer')
                <!-- / Footer -->


                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>


    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>


    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

</div>
<!-- / Layout wrapper -->


<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->

@include('Admin.includes.js')
@yield('js')
@vite('resources/js/app.js')
</body>

</html>

<!-- beautify ignore: end -->

