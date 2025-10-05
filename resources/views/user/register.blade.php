@extends('user.layouts.master')
@section('title')
    انشاء حساب جديد
@endsection
@section('content')
    <style>
        .btn-login {
            transition: color .15s;
            color: black;

            &:focus {
                color: #fff !important;
            }

            &:hover {
                text-decoration: underline !important;
                color: #e99239 !important;
            }
        }
    </style>
    <div class="container my-5 pt-3">
        <div class="d-flex align-items-start row g-3 pt-5">
            <div class="nav flex-column nav-pills me-3 col-md-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <button class="nav-link active" id="v-pills-Login-tab" data-bs-toggle="pill" data-bs-target="#v-pills-Login"
                    type="button" role="tab" aria-controls="v-pills-Login" aria-selected="true">
                    انشاء حساب
                </button>
                <div class="mt-3 d-flex flex-column align-items-center justify-content-center">
                    <p class="m-0 text-black fs-5">مسجل بالفعل؟</p>
                    <a class="nav-link btn-login text-center" href="{{ route('user.login.user') }}">
                        !سجل دخول الان
                    </a>
                </div>
            </div>
            <div class="tab-content col-md-8" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-Login" role="tabpanel"
                    aria-labelledby="v-pills-Login-tab">
                    <div class="card text-dark bg-light mb-3">
                        <div class="card-header">انشاء حساب <strong></strong></div>
                        <div class="card-body">
                            <form action="{{ route('user.register.submit') }}" method="POST" novalidate>
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم</label>
                                    <input type="text" id="name" name="name" class="form-control" />
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- <div class="mb-3">
                                    <label for="phone" class="form-label"> رقم الهاتف</label>
                                    <input type="number" name="phone" required class="form-control" id="phone"
                                        value="{{ old('phone') }}" />
                                </div> --}}
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">البريد الالكتروني</label>
                                    <input type="email" name="email" class="form-control"
                                        id="exampleFormControlInput1" value="{{ old('email') }}" />
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">العنوان بالكامل</label>
                                    <input type="address" name="address" required class="form-control"
                                        id="exampleFormControlInput1" value="{{ old('address') }}" />
                                </div> --}}
                                <div class="mb-3">
                                    <label for="password" class="form-label">كلمة المرور</label>
                                    <input type="password" name="password" class="form-control" id="password" />
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        id="password_confirmation" />
                                </div>
                                <div class="mb-3">
                                    <input type="submit" value="تسجيل" name="register"
                                        class="form-control bg-primary text-light" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
