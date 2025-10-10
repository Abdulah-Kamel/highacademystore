@extends('admin.layouts.master')
@section('title')
    barcode orders
@endsection
@section('content')
    <style>
        table th,
        tr,
        td {
            font-size: 14px;
        }

        .back-btn {
            margin-bottom: 20px;
        }

        .shipping-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>

    <!-- Back Button and Shipping Info -->
    <div class="row mb-3 mt-5">
        <div class="col-12">
            <a href="{{ route('dashboard.orders.barcode') }}" class="btn btn-secondary back-btn">
                <i class="fa fa-arrow-right"></i> العودة لاختيار طريقة الشحن
            </a>
        </div>
    </div>

    @if (request('shipping') && request('shipping') !== 'all')
        @php
            $shippingType = request('shipping');
            $typeLabels = [
                'branch' => 'استلام من الفرع',
                'post' => 'البريد المصري',
                'home' => 'التوصيل للمنزل',
            ];
            $typeIcons = [
                'branch' => 'fa-building',
                'post' => 'fa-envelope',
                'home' => 'fa-home',
            ];
        @endphp
        <div class="shipping-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5><i class="fa {{ $typeIcons[$shippingType] ?? 'fa-truck' }}"></i>
                        {{ $typeLabels[$shippingType] ?? $shippingType }}</h5>
                    <p class="mb-0">عرض طلبات {{ $typeLabels[$shippingType] ?? $shippingType }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fa fa-barcode fa-3x"></i>
                </div>
            </div>
        </div>
    @else
        <div>
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5><i class="fa fa-list"></i> جميع الطلبات</h5>
                    <p class="mb-0">عرض طلبات جميع طرق الشحن</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fa fa-barcode fa-3x"></i>
                </div>
            </div>
    @endif

    <!-- Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                @if (request('shipping') != 'branch')
                    <div class="card-header bg-primary">
                        <h6 class="card-title mb-0 text-white">
                            <i class="fa fa-list"></i> الطلبات والباركود
                        </h6>
                    </div>
                @endif
                
                <div class="card-body">
                    <table class="table table-hover align-middle mb-0" id="myTable">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>الاسم</th>
                                <th>الباركود</th>
                                <th>أضافه الباركود</th>
                                @if (request('shipping') === 'branch')
                                    <th>حالة التتبع</th>
                                    <th>الإجراءات</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "الكل"]
                ],
                "paging": true,
                "pageLength": 10,
                "stateSave": true,
                "stateDuration": -1,
                'scrollX': true,
                "processing": true,
                "serverSide": true,
                "sort": false,
                "ajax": {
                    "url": "{{ route('dashboard.orders.datatable') }}",
                    "type": "GET",
                    "data": function(d) {
                        d.state = "success";
                        @if (request('shipping') && request('shipping') !== 'all')
                            d.shipping = "{{ request('shipping') }}";
                        @endif
                    }
                },
                "columns": [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'barcode',
                        name: 'barcode'
                    },
                    {
                        data: 'admin_addbarcode',
                        name: 'admin_addbarcode',
                    },
                    @if (request('shipping') === 'branch')
                    {
                        data: 'tracker_state',
                        name: 'tracker_state',
                    },
                    {
                        data: 'branch_actions',
                        name: 'branch_actions',
                    },
                    @endif
                ],
            });
        });

        // Individual notification button click
        $(document).on('click', '.send-notification', function() {
            const orderId = $(this).data('order-id');
            const button = $(this);
            
            // Prompt for custom message
            const customMessage = prompt('اكتب رسالة مخصصة للعميل:', 'طلبك جاهز للاستلام من الفرع');
            
            if (!customMessage || customMessage.trim() === '') {
                alert('الرسالة المخصصة مطلوبة');
                return;
            }

            // Disable button and show loading
            button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> جاري الإرسال...');

            $.post('{{ route('dashboard.orders.sendIndividualNotification') }}', {
                    order_id: orderId,
                    custom_message: customMessage.trim(),
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    alert(response.message);
                    
                    // Reload DataTable to show updated status
                    $('#myTable').DataTable().ajax.reload();
                })
                .fail(function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response?.message || 'حدث خطأ أثناء إرسال الإشعار');
                })
                .always(function() {
                    // Re-enable button (will be updated by DataTable reload)
                    button.prop('disabled', false);
                });
        });

        // Handle status change dropdown
        function handleStatusChange(selectElement) {
            let selectedValue = selectElement.value;
            if (selectedValue.startsWith("http")) {
                window.location.href = selectedValue;
            } else {
                let orderId = selectElement.getAttribute('data-order-id');
                
                $.post('{{ route('dashboard.changestate') }}', {
                    id: orderId,
                    state: selectedValue,
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response.success) {
                        alert(response.msg);
                        $('#myTable').DataTable().ajax.reload();
                    } else {
                        alert('حدث خطأ: ' + response.msg);
                    }
                })
                .fail(function(xhr) {
                    alert('حدث خطأ أثناء تحديث حالة الطلب');
                })
                .always(function() {
                    // Reset dropdown
                    selectElement.selectedIndex = 0;
                });
            }
        }
    </script>
@endsection
