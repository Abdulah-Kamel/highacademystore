@extends('user.layouts.master') @section('title') Register  @endsection
@section('content')
<style>
.btn-login{
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
                انشاء حساب
            </button>
            <a
                class="nav-link btn-login mt-3 text-center text-decoration-none"
               href="{{ route('user.login.user') }}"
            >
                تسجيل دخول
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
                    <div class="card-header">انشاء حساب <strong></strong></div>
                    <div class="card-body">
                        <form
                        action="{{ route('user.register.submit') }}"
                        method="POST"
                        onsubmit="concatNames()"
                    >
                        @csrf @include('user.partials._errors')
                        <div class="mb-3">
                            <label for="first_name" class="form-label"
                                >الاسم الأول</label
                            >
                            <input
                                type="text"
                                id="first_name"
                                required
                                class="form-control"
                            />
                        </div>
        
                        <div class="mb-3">
                            <label for="middle_name" class="form-label"
                                >الاسم الأوسط</label
                            >
                            <input
                                type="text"
                                id="middle_name"
                                required
                                class="form-control"
                            />
                        </div>
        
                        <div class="mb-3">
                            <label for="last_name" class="form-label"
                                >اسم العائلة</label
                            >
                            <input
                                type="text"
                                id="last_name"
                                required
                                class="form-control"
                            />
                        </div>
                        <!-- Hidden input to store concatenated full name -->
                        <input type="hidden" name="name" id="full_name" />
        
                        <div class="mb-3">
                            <label for="phone" class="form-label"> رقم الهاتف</label>
                            <input
                                type="number"
                                name="phone"
                                required
                                class="form-control"
                                id="phone"
                                value="{{ old('phone') }}"
                            />
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label"
                                >البريد الالكتروني</label
                            >
                            <input
                                type="email"
                                name="email"
                                required
                                class="form-control"
                                id="exampleFormControlInput1"
                                value="{{ old('email') }}"
                            />
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label"
                                >العنوان بالكامل</label
                            >
                            <input
                                type="address"
                                name="address"
                                required
                                class="form-control"
                                id="exampleFormControlInput1"
                                value="{{ old('address') }}"
                            />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input
                                type="password"
                                name="password"
                                required
                                class="form-control"
                                id="password"
                            />
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label"
                                >تأكيد كلمة المرور</label
                            >
                            <input
                                type="password"
                                name="password_confirmation"
                                required
                                class="form-control"
                                id="password_confirmation"
                            />
                        </div>
                        <div class="mb-3">
                            <input
                                type="submit"
                                value="تسجيل"
                                name="register"
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
<script>
    function concatNames() {
        // Get values from the individual name inputs
        const firstName = document.getElementById("first_name").value;
        const middleName = document.getElementById("middle_name").value;
        const lastName = document.getElementById("last_name").value;

        // Concatenate the names into full name
        const fullName = firstName + " " + middleName + " " + lastName;

        // Set the concatenated name to the hidden input
        document.getElementById("full_name").value = fullName;
        console.log(fullName);
        
    }
</script>
