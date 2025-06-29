<form id="formAdd" action="{{route('grades.store')}}" method="post">
    @csrf
    <div class="row">
        <div class="col mb-12">
            <label for="nameEx7" class="form-label">الاسم</label>
            <input class="form-control" name="name" placeholder="ادخل الاسم" type="text" value=""
                   data-validation="required">
        </div>
    </div>
    <div class="col mb-12 mt-3">

        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            اغلاق
        </button>
        <button type="submit" class="btn btn-primary">حفظ</button>
    </div>
</form>

@include("Admin.grades.jsGrade")

