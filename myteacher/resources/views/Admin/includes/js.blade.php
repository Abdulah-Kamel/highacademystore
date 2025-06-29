{{--<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>--}}
<!-- Main JS -->
<script src="{{asset('Admin/vuexy-html-admin-template/assets/js/main.js')}}"></script>
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/node-waves/node-waves.js')}}"></script>
<script
    src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/hammer/hammer.js')}}"></script>
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/i18n/i18n.js')}}"></script>
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/js/menu.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/swiper/swiper.js')}}"></script>
<script
    src="{{asset('Admin/vuexy-html-admin-template/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
{{--<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>--}}


{{--<!-- SweetAlert2 -->--}}
{{--<script src="{{asset('Admin/vuexy-html-admin-template')}}/assets/sweetalert2/sweetalert2.min.js"></script>--}}
{{--<!-- Toastr -->--}}
{{--<script src="{{asset('Admin/vuexy-html-admin-template')}}/assets/toastr/toastr.min.js"></script>--}}
{{--<!-- Bootstrap 4 -->--}}
{{--<script src="{{asset('Admin/vuexy-html-admin-template')}}/assets/bootstrap/js/bootstrap.bundle.min.js"></script>--}}
{{--<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.js"></script>--}}
{{--<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function () {
        $('.js-example-basic-multiple').select2();
    });


</script>

@if(!\Illuminate\Support\Facades\Route::currentRouteNamed('get_settings_form'))
    @if(\Illuminate\Support\Facades\Auth::guard('student')->check()
        && \Illuminate\Support\Facades\Hash::check(@\Illuminate\Support\Facades\Auth::guard('student')->user()->phone,@\Illuminate\Support\Facades\Auth::guard('student')->user()->password) )
        <script>
            setTimeout(function () {
                Swal.fire({
                    html: 'مرحبا بك في منصة مدرسي<br>يجب اولا تغير كلمة المرور',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#8454dc',
                    confirmButtonText: 'موافق',
                    showCancelButton: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{route('get_settings_form')}}"
                    }
                })
            }, 1000)
        </script>
    @endif
@endif
<script>
    $(document).ready(function () {
        $('.notificationIconContent').on('click', function () {
            $.ajax({
                url: '{{route('read_notifications')}}',
                method: 'Get',
                success: function (response) {
                    $('.notificationsContent').html(response.notificationsContentHtml);
                    $('.notificationIconContent').html(response.notificationsIconHtml);
                }
            })
        })
    })

</script>
