@extends('admin.layouts.master')
@section('title', 'Offers')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>قائمة العروض</h4>
            <a href="{{ route('dashboard.create.offers') }}" class="btn btn-primary">إضافة عرض جديد</a>
        </div>
        <div class="card-body">
            <table class="table" id="offersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Set up CSRF token for AJAX requests
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    // Log DataTable initialization
    console.log('Initializing DataTable...');
    
    const table = $('#offersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('dashboard.offers.datatable') }}",
            type: 'GET',
            dataType: 'json',
            data: function(d) {
                // Add any additional parameters here if needed
                console.log('Sending request to server with parameters:', d);
            },
            dataSrc: function(json) {
                console.log('Received response from server:', json);
                return json.data;
            },
            error: function(xhr, error, thrown) {
                console.group('DataTables Error');
                console.error('Status:', xhr.status);
                console.error('Error:', error);
                console.error('Thrown:', thrown);
                console.error('Response:', xhr.responseText);
                console.groupEnd();
                
                if (xhr.status === 401) {
                    // Redirect to login if unauthorized
                    window.location.href = '{{ route("login") }}';
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while loading the data. Please check the console for details.',
                        confirmButtonText: 'OK'
                    });
                }
            }
        },
        columns: [
            { 
                data: 'id', 
                name: 'id',
                searchable: true,
                className: 'text-center'
            },
            { 
                data: 'image', 
                name: 'image', 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data || '<span class="text-muted">No Image</span>';
                    }
                    return data;
                }
            },
            { 
                data: 'operation', 
                name: 'operation', 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data || '';
                    }
                    return data;
                }
            }
        ]
    });

    // Delete Offer
    $('#offersTable').on('click', '.delete_btn', function() {
        const offerId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('dashboard.offers.destroy') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: offerId
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', response.success, 'success');
                        table.ajax.reload(); // ✅ Reload DataTable after deletion
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.error || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });
    
});
</script>
@endsection
