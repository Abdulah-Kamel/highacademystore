@extends('admin.layouts.master')
@section('title')
categories
@endsection
@section('content')
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">Patients Status</h6>
            <div class="dropdown morphing scale-left">
                <a href="#" class="card-fullscreen" data-bs-toggle="tooltip" title="Card Full-Screen"><i
                        class="icon-size-fullscreen"></i></a>
                <a href="#" class="more-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                        class="fa fa-ellipsis-h"></i></a>
                <ul class="dropdown-menu shadow border-0 p-2">
                    <li><a class="dropdown-item" href="#">File Info</a></li>
                    <li><a class="dropdown-item" href="#">Copy to</a></li>
                    <li><a class="dropdown-item" href="#">Move to</a></li>
                    <li><a class="dropdown-item" href="#">Rename</a></li>
                    <li><a class="dropdown-item" href="#">Block</a></li>
                    <li><a class="dropdown-item" href="#">Delete</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body" id="dataaa">
            <table class="table table-hover align-middle mb-0" id="myTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        {{-- <th>Is Parent</th>
                        <th>Parent ID</th> --}}
                        <th>الصوره</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Update Category Modal Start --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="category-modal">

            </div>

        </div>
    </div>
</div>
{{-- Update Category Modal Start --}}
@endsection
@section('js')
<script>
    $('#is_parent').change(function(e) {
        e.preventDefault();
        var is_checked = $('#is_parent').prop('checked');
        if (is_checked) {
            $('#parent_cat_div').addClass('d-none');
            $('#parent_cat_div').val('');
        } else {
            $('#parent_cat_div').removeClass('d-none');
        }
    });
</script>
<script>
    $.validate({
        form: 'form'
    });
    $(document).ready(function () {
        $(document).on('click','.editBtn',function(e) {
            var category_id = $(this).val();

            $('#editModal').modal('show');

            var url = "{{ route('dashboard.category.edit', ':id') }}";
            url = url.replace(':id', category_id);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('body #category-modal').html(response.categoryData);
                }
            });
        });
    });
    $(document).ready(function() {
    $('#form').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: '{{route('dashboard.category.update')}}',
            type: "POST",
            dataType: "json",
            data: formData,
            contentType: false,
            processData: false,

            success: function(response) {
                console.log(response);
                $('#editModal').modal('hide');
                Swal.fire('Data has been Updated successfully', '', 'success');
                location.reload();
            },

            error: function(xhr, status, error) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';
                $.each(errors, function(key, value) {
                    errorMessage += value[0] + '<br>';
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorMessage,
                });
            }

        });
    });
});
</script>
<script>
    $(document).ready(function () {

        $('#myTable').DataTable({
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "الكل"] ],
            "processing": true,
            "serverSide": true,
            "sort": false,
            "ajax": {
                "url": "{{ route('dashboard.category.datatable') }}",
                "type": "GET"
            },
            "columns": [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                // { data: 'is_parent', name: 'is_parent' },
                // { data: 'parent_id', name: 'parent' },
                { data: 'photo', name: 'photo' },
                { data: 'operation', name: 'operation', orderable: false }
            ],

        });
    })
</script>
<script>
    $(document).ready(function() {
        $(document).on('click','.delete_btn',function(e) {
            e.preventDefault();

            var category_id = $(this).attr('category_id');
            $.ajax({
                url: '{{route('dashboard.category.destroy')}}',
                type: "POST",
                dataType: "json",
                data: {
                    '_token': "{{csrf_token()}}",
                    'id': category_id
                },
                success: function(data) {
                    $('.brandrow'+data.id).remove();
                    Swal.fire('Data has been Deletd successfully', '', 'success');
                }
            });
        });
    });
</script>

@endsection
