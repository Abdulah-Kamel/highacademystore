<label for="nameEx7" class="form-label"> الحصة</label>
<select id="nameEx7" name="sessionsIDS[]" class="select2 form-select" multiple
        data-placeholder="اختر الحصة">
    @foreach($sessions as $session)
        <option value="{{$session->id}}">{{$session->name}}</option>
    @endforeach
</select>
<script src="{{asset('Admin/vuexy-html-admin-template')}}/assets/vendor/libs/select2/select2.js"></script>
<script
    src="{{asset('Admin/vuexy-html-admin-template')}}/assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
<script src="{{asset('Admin/vuexy-html-admin-template')}}/assets/js/forms-selects.js"></script>
