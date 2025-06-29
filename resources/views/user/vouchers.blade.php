@extends('user.layouts.master')
@section('title')
اكواد المدرسين
@endsection

@section('content')
<style>
   .form-control, .btn{
       border-radius:9px;
   } 
</style>
    <!-- Shop Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5 pt-5 mt-5">
            <!-- Shop Product Start -->
            <div class="col-12">
               <div class="col-12 pb-1 card p-2">
    <form action="{{ route('user.evouchers') }}" method="GET">
    <div class="row g-2">
        <div class="col-lg-4 col-md-4 col-12">
            <input name="name" class="form-control" placeholder="ابحث عن كوبون" value="{{ request('name') }}">
        </div>
        <div class="col-lg-4 col-md-4 col-12">
            <select name="type" class="form-control">

                <option value="">كل الكوبونات</option>
                <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>اسبوعي</option>
                <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>شهري</option>
                <option value="package" {{ request('type') == 'package' ? 'selected' : '' }}>باقة</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-2 col-12">
            <button type="submit" class="btn btn-outline-primary w-100">بحث</button>
        </div>
    </div>
</form>

</div>

                <div class="row pb-3 g-5 mt-3">

                    @include('user.layouts.voucher')
                </div>
            </div>
            <!-- Shop Product End -->
        </div>
    </div>
    <!-- Shop End -->
@endsection

@section('js')
  
@endsection
