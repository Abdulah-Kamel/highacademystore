@extends('admin.layouts.master')
@section('title', 'Offers')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Offers List</h4>
            <a href="{{ route('dashboard.create.offers') }}" class="btn btn-primary">Add New Offer</a>
        </div>
        <div class="card-body">
            <table class="table" id="offersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Discount Type</th>
                        <th>Discount Value</th>
                        <th>Min Books</th>
                        <th>Actions</th>
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
    const table = $('#offersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('dashboard.offers.datatable') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'type', name: 'type' },  // ✅ Show Discount Type
            { data: 'value', name: 'value' }, // ✅ Show Discount Value
            { data: 'minimum_books', name: 'minimum_books' }, // ✅ Show Min Books Required
            { data: 'operation', name: 'operation', orderable: false, searchable: false }
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
