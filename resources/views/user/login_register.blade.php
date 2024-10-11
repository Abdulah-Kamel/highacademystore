@extends('user.layouts.master') @section('title') High Academy Store @endsection
@section('content')
<style>
    .register{
    transition: background-color .15s,color .15s;
    color: black;
    &:focus{
        color: #fff !important;
        background-color: #e99239;
    }
    &:hover{
        color: #fff;
        background-color: #e99239;
    }
}
</style>
</style>
<div class="container my-5">
    <div class="d-flex align-items-start row g-3">
        <div
            class="nav flex-column nav-pills me-3 col-md-2"
            id="v-pills-tab"
            role="tablist"
            aria-orientation="vertical"
        >
            <button
                class="nav-link active"
                id="v-pills-Login-tab"
                data-bs-toggle="pill"
                data-bs-target="#v-pills-Login"
                type="button"
                role="tab"
                aria-controls="v-pills-Login"
                aria-selected="true"
            >
                تسجيل دخول
            </button>
            <a
            href="{{ route('user.register.user') }}"
                class="nav-link register text-center mt-2"
            >
                انشاء حساب
            </a>
        </div>
        <div class="tab-content col-md-8" id="v-pills-tabContent">
            <div
                class="tab-pane fade show active"
                id="v-pills-Login"
                role="tabpanel"
                aria-labelledby="v-pills-Login-tab"
            >
                <div class="card text-dark bg-light mb-3">
                    <div class="card-header">تسجيل دخول <strong></strong></div>
                    <div class="card-body">
                        <form
                            action="{{ route('user.login.submit') }}"
                            method="post"
                        >
                            @csrf @include("user.partials._errors")
                            <div class="mb-3">
                                <label
                                    for="exampleFormControlInput1"
                                    class="form-label"
                                    >البريد الالكتروني</label
                                >
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    required
                                    id="exampleFormControlInput1"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"
                                    >كلمة المرور</label
                                >
                                <input
                                    type="password"
                                    name="password"
                                    required
                                    class="form-control"
                                    id="password"
                                />
                            </div>
                            <a href="{{ route('password.request') }}"
                                >نسيت كلمة المرور</a
                            >
                            <div class="mb-3">
                                <input
                                    type="submit"
                                    value="تسجيل"
                                    name="login"
                                    class="form-control bg-primary text-light"
                                />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

