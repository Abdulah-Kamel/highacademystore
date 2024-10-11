@extends('user.layouts.master')
@section('title')
High Academy Store || reset password
@endsection
@section('content')
<div class="container my-5">
    <div class="d-flex align-items-start row">

        <div class="tab-content  col-md-12" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-Login" role="tabpanel"
                aria-labelledby="v-pills-Login-tab">
                <div class="card text-dark bg-light mb-3">
                    <div class="card-header">اعادة تعيين كلمة المرور<strong></strong></div>
                    <div class="card-body">


                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            @include('user.partials._errors')
                            <div class="mb-3">
                                <input name="email" type="email" value="{{ $email }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="البريد الالكتروني" readonly />
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <input type="password" class="form-control mt-4 @error('password') is-invalid @enderror"
                                    placeholder="كلمة المرور" name="password" />
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <input type="password"
                                    class="form-control mt-4 @error('password_confirmation') is-invalid @enderror"
                                    placeholder="اعادة كلمة المرور" name="password_confirmation" />
                                @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <input type="submit" value="ارسال" name="reset"
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