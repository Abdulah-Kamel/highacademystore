
<h4 class="onboarding-title text-body">اضافة ادمن جديد</h4>
<form id="formAdd" action="{{route('admins.store')}}" method="post">
    @csrf
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="nameEx7" class="form-label">الاسم</label>
                <input class="form-control" name="name" placeholder="ادخل الاسم" type="text" value=""
                       data-validation="required">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="nameEx7" class="form-label" style="float: right;">البريد الالكتروني</label>
                <input class="form-control" name="email" placeholder="ادخل البريد الالكتروني" type="text" value="" data-validation="required">
            </div>


        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="nameEx7" class="form-label label-ltr" >رقم الهاتف</label>
                <input class="form-control" name="phone" placeholder="ادخل رقم الهاتف" type="text" value=""
                       data-validation="required ,length" data-validation-length="max11">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="nameEx7" class="form-label label-ltr" >الصورة</label>
                <input class="form-control" name="image" placeholder="اختر الصورة" type="file" value=""
                       data-validation="required">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="nameEx7" class="form-label">كلمة المرور</label>
                <input class="form-control" name="password" placeholder="ادخل كلمة المرور" type="password" value=""
                       data-validation="required , length , confirmation" data-validation-length="min8">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="nameEx7" class="form-label"> تاكيد كلمة المرور</label>
                <input class="form-control" name="password_confirmation" placeholder="قم بتاكيد كلمة المرور" type="password" value=""
                       data-validation="required" >
            </div>
        </div>
    </div>
   <div class="mb-3">
       <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
           اغلاق
       </button>
       <button type="submit" class="btn btn-primary">حفظ</button>
   </div>
</form>
@include('Admin.admins.js')

