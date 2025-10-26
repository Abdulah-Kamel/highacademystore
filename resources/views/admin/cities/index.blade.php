@extends('admin.layouts.master')
@section('title')
    إدارة المدن
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    إدارة المدن
                    @if($governorate)
                        - {{ $governorate->name_ar }}
                    @endif
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="card-title mb-0">المدن</h5>
                        @if(!$governorate)
                            <select id="governorateFilter" class="form-select" style="width: 200px;">
                                <option value="">جميع المحافظات</option>
                                @foreach($governorates as $gov)
                                    <option value="{{ $gov->id }}">{{ $gov->name_ar }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success" onclick="importFromJson()">
                            <i class="fa fa-download me-2"></i>استيراد من JSON
                        </button>
                        <a href="{{ route('dashboard.cities.create', $governorate ? ['governorate' => $governorate->id] : []) }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>إضافة مدينة جديدة
                        </a>
                    </div>
                </div>

                <table class="table table-hover align-middle mb-0" id="citiesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم بالعربية</th>
                            <th>الاسم بالإنجليزية</th>
                            @if(!$governorate)
                                <th>المحافظة</th>
                            @endif
                            <th>الحالة</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data will be loaded via AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        var columns = [
            { data: 'id', name: 'id' },
            { data: 'name_ar', name: 'name_ar' },
            { data: 'name_en', name: 'name_en' }
        ];

        @if(!$governorate)
            columns.push({ data: 'governorate', name: 'governorate' });
        @endif

        columns.push(
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        );

        var table = $('#citiesTable').DataTable({
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "الكل"]],
            "pageLength": 10,
            "stateSave": true,
            "stateDuration": -1,
            'scrollX': true,
            "processing": true,
            "serverSide": true,
            "sort": false,
            "ajax": {
                "url": "{{ route('dashboard.cities.datatable') }}",
                "type": "GET",
                "data": function(d) {
                    @if($governorate)
                        d.governorate = {{ $governorate->id }};
                    @else
                        d.governorate = $('#governorateFilter').val();
                    @endif
                }
            },
            "columns": columns,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
            }
        });

        @if(!$governorate)
            $('#governorateFilter').on('change', function() {
                table.ajax.reload();
            });
        @endif
    });

    function deleteCity(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'لن تتمكن من استرجاع هذه المدينة بعد الحذف!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("dashboard.cities.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('تم الحذف!', response.message, 'success');
                            $('#citiesTable').DataTable().ajax.reload();
                        } else {
                            Swal.fire('خطأ!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء حذف المدينة', 'error');
                    }
                });
            }
        });
    }

    function importFromJson() {
        Swal.fire({
            title: 'استيراد المدن',
            text: 'هل تريد استيراد المدن من ملف JSON؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، استورد!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("dashboard.cities.import-json") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('تم الاستيراد!', response.message, 'success');
                            $('#citiesTable').DataTable().ajax.reload();
                        } else {
                            Swal.fire('خطأ!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء استيراد المدن', 'error');
                    }
                });
            }
        });
    }
</script>
@endsection
