@extends('user.layouts.master')

@section('title')
    السلة
@endsection


@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="{{ route('user.home') }}">الرئيسيه</a>
                    <span class="breadcrumb-item active">سلة التسوق</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Cart section -->
    <div class="container my-5" id="cart-list">
        @include('user.layouts._cart-list')

    </div>

@endsection
