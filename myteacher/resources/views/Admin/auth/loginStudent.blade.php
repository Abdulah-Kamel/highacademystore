<!DOCTYPE html>
<html lang="ar" class="light-style layout-wide  customizer-hide" dir="rtl" data-theme="theme-default"
      data-assets-path="{{asset('Admin')}}/vuexy-html-admin-template/assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title> تسجيل الدخول (الطلاب)</title>
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 5"/>
    <meta name="keywords" content="dashboard, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5">
    <!-- Canonical SEO -->
    <link rel="canonical" href="https://1.envato.market/vuexy_admin">
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-5J3LMKC');</script>
    <!-- End Google Tag Manager -->

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
          href="{{asset('Admin')}}/vuexy-html-admin-template/assets/img/favicon/favicon.ico"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/fonts/fontawesome.css"/>
    <link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/fonts/tabler-icons.css"/>
    <link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/fonts/flag-icons.css"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/css/rtl/core.css"
          class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/css/rtl/theme-default.css"
          class="template-customizer-theme-css"/>
    <link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/css/demo.css"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet"
          href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/node-waves/node-waves.css"/>
    <link rel="stylesheet"
          href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
    <link rel="stylesheet"
          href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/typeahead-js/typeahead.css"/>
    <!-- Vendor -->
    <link rel="stylesheet"
          href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/@form-validation/umd/styles/index.min.css"/>
    <link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/toastr/toastr.css"/>

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/css/pages/page-auth.css">

    <!-- Helpers -->
    <script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/js/config.js"></script>
    <script src="http://demo.javatpoint.com/plugin/jquery.js"></script>
    <link rel="stylesheet" href="http://demo.javatpoint.com/plugin/bootstrap-3.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css'>
    <link rel="stylesheet" href="{{asset('/Admin/vuexy-html-admin-template/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
    <link rel="stylesheet" href="{{asset('/Admin/vuexy-html-admin-template/assets/vendor/libs/toastr/toastr.css')}}" />
</head>

<body>

<!-- ?PROD Only: Google Tag Manager (noscript) (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5DDHKGP" height="0" width="0"
            style="display: none; visibility: hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- Content -->
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <!-- Login -->
            <div class="card">
                <div class="card-body">
                    <div class="app-brand justify-content-center mb-4 mt-2">
                        <h3>اهلا بك في منصة <span style="font-weight: bold ; color: #4a488c"> مدرسي</span> </h3>
                    </div>
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4 mt-2">
                        <a href="{{route('loginFormStudent')}}" class="app-brand-link gap-2">
                            <img src="{{asset('Admin')}}/vuexy-html-admin-template/assets/img/teacher.webp" alt=""
                                 width="150px" height="150px">
                        </a>
                    </div>
                    <!-- /Logo -->
                    <form id="formLogin" class="mb-3" action="{{route('loginStudent')}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">رقم الهاتف </label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                   placeholder="اكتب رقم الهاتف" autofocus data-validation="required">
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">كلمة المرور</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                       placeholder="اكنب كلمة مرورك"
                                       data-validation="required , length" data-validation-length="min8"/>
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" id="btnlogin" type="submit">تسجيل الدخول</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
</div>

<!-- / Content -->


<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/jquery/jquery.js"></script>
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/popper/popper.js"></script>
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/js/bootstrap.js"></script>
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/node-waves/node-waves.js"></script>
<script
    src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/hammer/hammer.js"></script>
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/i18n/i18n.js"></script>
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/typeahead-js/typeahead.js"></script>
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/js/menu.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script
    src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
<script
    src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
<script
    src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>

<!-- Main JS -->
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/js/main.js"></script>
<!-- Page JS -->
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/js/pages-auth.js"></script>
<script src="{{asset('Admin')}}/form-validator/jquery.form-validator.min.js"></script>
<script src="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/toastr/toastr.js"></script>

<script>
    $.validate({
        modules: 'security, date',
        lang: 'ar'
    });
</script>
<script>
    $(document).ready(function () {
        $('#formLogin').on('submit', function (e) {
            var url = $(this).attr('action');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            e.preventDefault();
            $.ajax({
                url: url,
                method: "POST",
                data: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    document.getElementById("btnlogin").innerHTML = 'جاري تسجيل الدخول';
                },
                success: function (response) {
                    if (response.success) {
                        toastr.options = {
                            positionClass: 'toast-top-left',
                        };
                        toastr.success(response.success)
                        setTimeout(function () {
                            window.location.href = response.redirect
                        }, 1500)
                    }
                    if (response.error) {
                        toastr.options = {
                            positionClass: 'toast-top-left',
                        };
                        toastr.error(response.error)

                    }
                },
            })
        })
    });
</script>
</body>

</html>

<!-- beautify ignore:end -->


