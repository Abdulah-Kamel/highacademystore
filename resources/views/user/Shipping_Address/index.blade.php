@extends('user.layouts.master')

@section('title')
    عناوين الشحن
@endsection

@section('content')
    <style>
        .custom-style {
            font-size: 16px;
            color: #333;
            padding: 10px;
            line-height: 1.5;
        }

        .custom-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .custom-link:hover {
            color: #0056b3;
        }

        .action-buttons {
            display: flex;
            width: 100%;
        }

        .action-buttons a {
            cursor: pointer;
            flex: 1;
            text-decoration: none;
            text-align: center;
            padding: 10px;
            color: white;
            font-size: 18px;
            font-weight: bold;
            /* border-radius: 0 0 20px 20px; */
            &:first-child {
                border-radius: 0 0 20px 0;
            }
            &:last-child {
                border-radius: 0 0 0 20px;
            }
        }

        .delete_btn {
            background-color: #dc3545;
        }

        .delete_btn:hover {
            background-color: #c82333;
        }

        .edit_btn {
            background-color: #007bff;
        }

        .edit_btn:hover {
            background-color: #0056b3;
        }
    </style>

    <div class="container my-5 pt-5" id="cart-list">
        <div class="row pt-5">
            <div class="col-md-12">
                <h5 class="section-title position-relative text-uppercase mb-3 text-center"><span class="pr-3">اختار البيانات اللي عايز تعدلها</span></h5>
            </div>
            <p class="text-center custom-style" dir="rtl">
                اضافه عنوان جديد <a href="{{ route('user.shipping.store') }}" class="custom-link">أضغط هنا</a>
            </p>
            @foreach ($addresses as $address)
                <div class="col-md-6">
                    <div class="card mt-3 mx-auto cardLink" style="width: 100%; border-radius: 20px;" dir="rtl">
                        <div class="card-body rounded-5">
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="title">المحافظة </strong>
                                <strong>{{ $address->governorate }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="title">المدينة</strong>
                                <strong>{{ $address->city }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="title">عنوان الشحن</strong>
                                <strong>{{ $address->address }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="title">الاسم ثلاثي</strong>
                                <strong>{{ $address->name }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="title">رقم الموبيل</strong>
                                <strong>{{ $address->mobile }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="title">رقم الموبيل الاحتياطي</strong>
                                <strong>{{ $address->temp_mobile }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="title">اسم اقرب مكتب بريد</strong>
                                <strong>{{ $address->near_post }}</strong>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <a user_address_id="{{ $address->id }}" class="delete_btn delete-button">حذف البيانات</a>
                            <a href="{{ route('user.shipping.edit', $address->id) }}" class="edit_btn">تعديل البيانات</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '.delete_btn', function(e) {
                e.preventDefault();
                var user_address_id = $(this).attr('user_address_id');
                $.ajax({
                    url: '{{ route('user.shipping.destroy') }}',
                    type: "POST",
                    dataType: "json",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': user_address_id
                    },
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم حذف البيانات بنجاح',
                            showConfirmButton: false,
                        });
                        setTimeout(function() {
                            window.location.href = '{{ route('user.shipping') }}';
                        }, 500);
                    }
                });
            });
        });
    </script>
@endsection
