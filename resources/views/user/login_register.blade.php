@extends('user.layouts.master')
@section('title')
High Academy Store
@endsection
@section('content')
<div class="container my-5">
    <div class="d-flex align-items-start row">
        <div class="nav flex-column nav-pills me-3 col-md-2" id="v-pills-tab" role="tablist"
            aria-orientation="vertical">
            <button class="nav-link active" id="v-pills-Login-tab" data-bs-toggle="pill" data-bs-target="#v-pills-Login"
                type="button" role="tab" aria-controls="v-pills-Login" aria-selected="true">تسجيل دخول</button>
            <button class="nav-link" id="v-pills-Register-tab" data-bs-toggle="pill" data-bs-target="#v-pills-Register"
                type="button" role="tab" aria-controls="v-pills-Register" aria-selected="false">انشاء حساب</button>
        </div>
        <div class="tab-content  col-md-8" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-Login" role="tabpanel"
                aria-labelledby="v-pills-Login-tab">
                <div class="card text-dark bg-light mb-3">
                    <div class="card-header">تسجيل دخول <strong></strong></div>
                    <div class="card-body">
                      <form action="{{route('user.login.submit')}}" method="post">
                            @csrf
                          @include("user.partials._errors")
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">البريد الالكتروني</label>
                                <input type="email" name="email" class="form-control" required
                                    id="exampleFormControlInput1">
                             
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" name="password" required class="form-control" id="password">
                              
                            </div>
                            <a href="{{route('password.request')}}">نسيت كلمة المرور</a>
                            <div class="mb-3">
                                <input type="submit" value="تسجيل" name="login"
                                    class="form-control bg-primary text-light">
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="v-pills-Register" role="tabpanel" aria-labelledby="v-pills-Register-tab"
                style="margin-top:10px">
                <div class="card text-dark bg-light mb-3">
                    <div class="card-header">تسجيل <strong></strong></div>
                    <div class="card-body">
                        <form action="{{route('user.register.submit')}}" method="POST">
                            @csrf
                            @include('user.partials._errors')
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم بالكامل</label>
                                <input type="text" name="name" required class="form-control" id="name"
                                    value="{{old('name')}}">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label"> رقم الهاتف</label>
                                <input type="number" name="phone" required class="form-control" id="phone"
                                    value="{{old('phone')}}">
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">البريد الالكتروني</label>
                                <input type="email" name="email" required class="form-control"
                                    id="exampleFormControlInput1" value="{{old('email')}}">
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">العنوان بالكامل</label>
                                <input type="address" name="address" required class="form-control"
                                    id="exampleFormControlInput1" value="{{old('address')}}">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" name="password" required class="form-control" id="password">
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation" required class="form-control"
                                    id="password_confirmation">
                            </div>
                            <div class="mb-3">
                                <input type="submit" value="تسجيل" name="register"
                                    class="form-control bg-primary text-light">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection