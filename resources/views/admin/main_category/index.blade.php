@extends('admin.layouts.master')

@section('title')
    Main Categories
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Main Categories</h6>
                <a href="{{ route('dashboard.create.main_categories') }}" class="btn btn-success">Add New Category</a>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle mb-0" id="categoriesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الصوره</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DataTables will populate this --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#categoriesTable').DataTable({
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "الكل"]
                ],
                "paging": true,
                "pageLength": 10,
                "stateSave": true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('dashboard.main_categories.datatable') }}", // ✅ Corrected route
                    "type": "GET"
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'icon_image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'operation',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        // ✅ DELETE CATEGORY FUNCTION
        $(document).on('click', '.delete_btn', function(e) {
            e.preventDefault();
            var category_id = $(this).attr('category_id');

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لا يمكنك التراجع بعد الحذف!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذفها!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('dashboard.main_categories.destroy') }}', // ✅ Corrected route
                        type: "POST",
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'id': category_id
                        },
                        success: function(data) {
                            $('#categoriesTable').DataTable().ajax.reload();
                            Swal.fire('تم الحذف!', 'تم حذف الفئة بنجاح.', 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('خطأ!', 'لم يتم الحذف، يرجى المحاولة لاحقًا.', 'error');
                            console.error("Delete Error:", xhr);
                        }
                    });
                }
            });
        });
    </script>
@endsection
