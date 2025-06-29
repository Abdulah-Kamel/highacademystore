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
                    <div class="card-header">نسيت كلمة المرور<strong></strong></div>
                    <div class="card-body">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            @include('user.partials._errors')
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">البريد الالكتروني</label>
                                <input type="email" name="email" class="form-control" required
                                    id="exampleFormControlInput1">
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
