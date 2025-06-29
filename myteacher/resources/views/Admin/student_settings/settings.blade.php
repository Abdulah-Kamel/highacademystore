@extends('Admin.layout.index')
@section('title')
    اعدادات الطالب
@endsection
@section('content')
    <form id="formAdd" action="{{route('change_password')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-6 mb-3">
                <label for="nameBasic" class="form-label">كلمة المرور الجديدة</label>
                <input type="password" class="form-control" name="password" data-validation="confirmation">
            </div>
            <div class="col-6 mb-3">
                <label for="nameBasic" class="form-label">تاكيد كلمة المرور الجديدة</label>
                <input type="password" class="form-control" name="password_confirmation" >
            </div>
            <div class="col-6 mb-3">
                <label for="nameBasic" class="form-label">تغير صورة البروفايل</label>
                <input type="file" class="form-control" name="image" data-validation="required">
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">حفظ</button>
    </form>
@endsection
@section('js')
    <script src="{{asset('Admin')}}/form-validator/jquery.form-validator.min.js"></script>
    <script>
        $.validate({
            modules: 'security, date',
            lang: 'ar'

        });
    </script>
@endsection
