<h4 class="onboarding-title text-body">تعديل مستخدم </h4>
<form id="formEdit" action="{{route('admins.update',$obj->id)}}" method="post"  enctype=multipart/form-data>
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="nameEx7" class="form-label">الاسم</label>
                <input class="form-control" name="name" placeholder="ادخل الاسم" type="text" value="{{$obj->name}}"
                       data-validation="required">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="nameEx7" class="form-label label-ltr">رقم الهاتف</label>
                <input class="form-control" name="phone" placeholder="ادخل رقم الهاتف" type="text"
                       value="{{$obj->phone}}"
                       data-validation="required ,length" data-validation-length="max11">
            </div>
        </div>
        <div class="col-sm-12">
            <div class="mb-3">
                <label for="nameEx7" class="form-label label-ltr">الصورة</label>
                <input class="form-control" name="image" placeholder="اختر الصورة" type="file" value="">
            </div>
        </div>
        <div class="col-sm-12">
            <div class="mb-3">
                <label for="nameEx7" class="form-label label-ltr"></label>
                <img src="{{asset('').$obj->image}}" width="200px" height="200px" style="border-radius: 50%;">
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


