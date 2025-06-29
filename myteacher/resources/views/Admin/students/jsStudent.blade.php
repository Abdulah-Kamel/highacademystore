<script src="{{asset('Admin/vuexy-html-admin-template')}}/assets/vendor/libs/select2/select2.js"></script>
<script
    src="{{asset('Admin/vuexy-html-admin-template')}}/assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
<script src="{{asset('Admin/vuexy-html-admin-template')}}/assets/js/forms-selects.js"></script>
<script>
    $(document).ready(function () {
        $('#grade_id').on('change', function (e) {
            var grade_id = $(this).val()
            var url = "{{route('sessions.show',':grade_id')}}"
            url = url.replace(':grade_id', grade_id);
            $.ajax({
                url: url,
                success: function (response) {
                    $('.sessions').html(response.html);
                }
            });
        })
    });
    $(document).ready(function () {
        $('#formAdd').on('submit', function (e) {
            var url = $(this).attr('action');
            e.preventDefault();
            $.ajax({
                url: url,
                method: "POST",
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        toastr.options = {
                            positionClass: 'toast-top-left',

                        };
                        toastr.success(response.success)
                        $('#basicModal').modal('hide');
                        myTable.ajax.reload();
                    }
                },
                error: function (xhr, status, error) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    $.each(errors, function (key, value) {
                        errorMessage += value[0] + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: '',
                        html: errorMessage,
                    });
                }
            })
        })
    });
    $(document).ready(function () {
        $('#formEdit').on('submit', function (e) {
            var url = $(this).attr('action');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            e.preventDefault();
            $.ajax({
                url: url,
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        toastr.options = {
                            positionClass: 'toast-top-left',
                        };
                        $('#basicModal').modal('hide');
                        toastr.success(response.success)
                        myTable.ajax.reload();
                    }
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    $.each(errors, function (key, value) {
                        errorMessage += value[0] + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: '',
                        html: errorMessage,
                    });
                }
            })
        })

    });
</script>
<script src="{{asset('')}}form-validator/jquery.form-validator.min.js"></script>
<script>
    $.validate({
        modules: 'security, date',
        lang: 'ar'
    });
    $.formUtils.addValidator({
        name: 'phoneNumber',
        validatorFunction: function (value, $el, config, language, $form) {
            // إزالة أي مسافات فارغة من الرقم
            var cleanedValue = value.replace(/\s/g, '');

            // التحقق من أن القيمة غير فارغة وتحتوي على 11 رقمًا وتبدأ بالصفر
            if (cleanedValue.length === 11 && cleanedValue[0] === '0' && !isNaN(cleanedValue)) {
                return true; // الرقم صحيح
            }

            return false; // الرقم غير صحيح
        },
        errorMessage: 'ادخل رقم هاتف صحيح، يجب أن يكون مكونًا من 11 رقم ويبدأ بالصفر'
    });


    // Initiate form validation
    $.validate();
</script>

