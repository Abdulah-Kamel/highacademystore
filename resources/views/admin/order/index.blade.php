@extends('admin.layouts.master')
@section('title')
    orders
@endsection
@section('content')
    <style>
        table th,
        tr,
        td {
            font-size: 20px:
        }
    </style>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">الطلبات</h6>
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
            <div class="card-body row">
                <!-- STATE FILTER -->
                <div class="dropdown mb-3 col-6">
                    <button class="btn btn-light dropdown-toggle w-100" id="stateFilterBtn" data-bs-toggle="dropdown">
                        <span id="stateFilterLabel">كل الحالات</span>
                    </button>
                    <ul class="dropdown-menu w-100  ">
                        <li><a href="#" class="dropdown-item state-filter-item" data-value="">كل الحالات</a></li>
                        <li><a href="#" class="dropdown-item state-filter-item" data-value="new">طلب جديد</a></li>
                        <li><a href="#" class="dropdown-item state-filter-item" data-value="reserved">طلب محجوز</a>
                        </li>
                        <li><a href="#" class="dropdown-item state-filter-item" data-value="success">طلب ناجح</a></li>
                        <li><a href="#" class="dropdown-item state-filter-item" data-value="pending">طلب معلق</a></li>
                        <li><a href="#" class="dropdown-item state-filter-item" data-value="cancelled">تم الإلغاء</a>
                        </li>
                    </ul>
                </div>

                <!-- SHIPPING FILTER -->
                <div class="dropdown mb-3 col-6">
                    <button class="btn btn-light dropdown-toggle w-100" id="shippingFilterBtn" data-bs-toggle="dropdown">
                        <span id="shippingFilterLabel">كل طرق الشحن</span>
                    </button>
                    <ul class="dropdown-menu w-100">
                        <li>
                            <a href="#" class="dropdown-item shipping-filter-item" data-value="">
                                كل طرق الشحن
                            </a>
                        </li>
                        @foreach ($shippingMethods as $method)
                            <li>
                                <a href="#" class="dropdown-item shipping-filter-item"
                                    data-value="{{ $method->id }}">
                                    {{ $method->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>



                <div class="container mt-3 d-flex flex-column align-items-center" style="max-width: 500px;">
                    <!-- اختيار حالة الطلبات -->


                    <div class="d-flex gap-2 w-100 mt-2">
                        <!-- إدخال عدد الطلبات -->
                        <div class="input-group w-50">
                            <input type="number" id="orderLimit" class="form-control" placeholder="اصدار الطلبات يدويا"
                                min="1" oninput="validateInput(this)">
                            <button class="btn btn-success" onclick="exportOrders()">تصدير</button>
                        </div>

                        <!-- إدخال عدد الطلبات بالتفاصيل -->
                        <div class="input-group w-50">
                            <input type="number" id="orderSuccessLimit" class="form-control"
                                placeholder=" اصدار الطلبات بالتفاصيل يدويا" min="1" oninput="validateInput(this)">
                            <button class="btn btn-success" onclick="exportOrdersSuccess()">تصدير</button>
                        </div>
                    </div>
                    <div class="w-100 mb-2">
                        <label for="orderGroupedLimit" class="form-label">تصدير الطلبات الناجحه بالتقصيل</label>
                        <div class="input-group">
                            <input type="number" id="orderGroupedLimit" class="form-control"
                                placeholder="عدد طلبات الفروع للتصدير" min="1" oninput="validateInput(this)">
                            <button class="btn btn-info text-white" onclick="exportGroupedOrders()">تصدير الطلبات
                                الناجحة</button>
                        </div>
                    </div>
                    <div class="w-100 mb-2">
                        <label for="orderBranchLimit" class="form-label">تصدير طلبات الفروع</label>
                        <div class="input-group">
                            <input type="number" id="orderBranchLimit" class="form-control" 
                                placeholder="عدد طلبات الفروع للتصدير" min="1" oninput="validateInput(this)">
                            <button class="btn btn-info text-white" onclick="exportBranchOrders()">تصدير طلبات الفروع</button>
                        </div>
                    </div>
                    <div class="w-100 mb-2">
                        <label for="orderStatus" class="form-label"> نوع الطلب</label>
                        <select id="orderStatus" class="form-select">
                            <option value="" disabled selected>اختر</option>
                            <option value="success">الطلبات الناجحة</option>
                            <option value="reserved">الطلبات المحجوزة</option>
                            <option value="pending">الطلبات المعلقة</option>
                        </select>
                    </div>
                    
                    <div class="w-100 mb-2">
                        <a href="#" onclick="confirmUpdateAllReversed()" class="form-control btn btn-danger">تحويل
                            جميع
                            الطلبات المحجوزة لحالة ناجح</a>
                    </div>


                </div>

                {{--                <script> --}}
                {{--                    function updateExportLinks() { --}}
                {{--                        let status = document.getElementById("orderStatus").value; --}}
                {{--                        let baseExportUrl = "{{ route('dashboard.orders.export') }}"; --}}
                {{--                        let baseSuccessExportUrl = "{{ route('dashboard.orders.export.success') }}"; --}}

                {{--                        if (status === "reserved") { --}}
                {{--                            document.getElementById("export").href = baseExportUrl + "/reserved/?limit=10"; --}}
                {{--                            document.getElementById("successExport").href = baseSuccessExportUrl + "/reserved/?limit=10"; --}}
                {{--                        } else { --}}
                {{--                            document.getElementById("export").href = baseExportUrl + "/?limit=10"; --}}
                {{--                            document.getElementById("successExport").href = baseSuccessExportUrl + "/?limit=10"; --}}
                {{--                        } --}}
                {{--                    } --}}

                {{--                    function validateInput(input) { --}}
                {{--                        if (input.value < 1) { --}}
                {{--                            input.value = 1; --}}
                {{--                        } --}}
                {{--                    } --}}

                {{--                    function exportOrders() { --}}
                {{--                        let limit = document.getElementById("orderLimit").value || 10; --}}
                {{--                        let status = document.getElementById("orderStatus").value; --}}
                {{--                        window.location.href = "{{ route('dashboard.orders.export') }}/" + status + "/?limit=" + limit; --}}
                {{--                    } --}}

                {{--                    function exportOrdersSuccess() { --}}
                {{--                        let limit = document.getElementById("orderSuccessLimit").value || 10; --}}
                {{--                        let status = document.getElementById("orderStatus").value; --}}
                {{--                        window.location.href = "{{ route('dashboard.orders.export.success') }}/" + status + "/?limit=" + limit; --}}
                {{--                    } --}}
                {{--                </script> --}}


                <br>
                <table class="table table-hover align-middle mb-0" id="myTable" dir="rtl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الرقم</th>
                            <th>عنوان الشحن</th>
                            <th>نوع الشحن</th>
                            <th>اجمالي المدفوع</th>
                            <th>وسيلة الدفع</th>
                            <th>رقم حساب الدفع</th>
                            <th>ايصال الدفع</th>
                            <th>حالة العملية</th>
                            <th>تغير الحالة</th>
                            <th>تعديل الطلب</th>
                            <th>التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function confirmUpdateAllReversed() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم تحويل جميع الطلبات المحجوزة إلى حالة ناجح. هل تريد المتابعة؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، قم بالتحويل',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('dashboard.update_all_reversed_order') }}";
                }
            });e
        }

        $(document).ready(function() {
            let stateFilterValue = '';
            let shippingFilterValue = '';

            const table = $('#myTable').DataTable({
                lengthMenu: [
                    [10, 25, 50, 100, 200, -1],
                    [10, 25, 50, 100, 200, "الكل"]
                ],
                paging: true,
                pageLength: 10,
                stateSave: true,
                stateDuration: -1,
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.orders.datatable') }}",
                    data: d => {
                        d.state = stateFilterValue;
                        d.shipping = shippingFilterValue;
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone',
                        name: 'mobile'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'shipping_method',
                        name: 'shipping_method'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'method',
                        name: 'method'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'change_status',
                        name: 'change_status'
                    },
                    {
                        data: 'edit_order',
                        name: 'edit_order'
                    },
                    {
                        data: 'details',
                        name: 'details'
                    },
                ]
            });

            function updateMainExportLinks() {
                var info = table.page.info();
                var lengthMenuSetting = info.length > 0 ? info.length : 10;
                let baseExportUrl = "{{ route('dashboard.orders.export') }}";
                let baseSuccessExportUrl = "{{ route('dashboard.orders.export.success') }}";

                let params = new URLSearchParams();
                params.append('limit', lengthMenuSetting);

                if (stateFilterValue) {
                    params.append('status', stateFilterValue);
                }
                if (shippingFilterValue) {
                    params.append('shipping', shippingFilterValue);
                }

                $('#export').attr('href', baseExportUrl + '?' + params.toString());

                // Disable success export if no specific state is selected
                if (stateFilterValue) {
                    $('#successExport').attr('href', baseSuccessExportUrl + '?' + params.toString()).removeClass('disabled');
                } else {
                    $('#successExport').attr('href', '#').addClass('disabled');
                }
            }

            // Initial call
            updateMainExportLinks();

            // Status filter click
            $('.state-filter-item').on('click', function(e) {
                e.preventDefault();
                stateFilterValue = $(this).data('value');
                $('#stateFilterLabel').text($(this).text());
                table.draw();
                updateMainExportLinks();
            });

            // Shipping filter click
            $('.shipping-filter-item').on('click', function(e) {
                e.preventDefault();
                shippingFilterValue = $(this).data('value');
                $('#shippingFilterLabel').text($(this).text());
                table.ajax.reload();
                updateMainExportLinks();
            });

            // Page length change
            $('#myTable_length select').on('change', function() {
                setTimeout(updateMainExportLinks, 100);
            });

            // Expose shippingFilterValue globally for manual export functions
            window.getShippingFilterValue = () => shippingFilterValue;
        });

        function handleStatusChange(selectElement) {
            let selectedValue = selectElement.value;
            if (selectedValue.startsWith("http")) {
                window.location.href = selectedValue;
            }
        }

        function exportOrders() {
            let limit = document.getElementById("orderLimit").value || 10;
            let status = $('#orderStatus').val();

            if (!status) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'يرجى تحديد نوع الطلب أولاً.',
                    icon: 'error',
                    confirmButtonText: 'حسنًا'
                });
                return;
            }

            let url = new URL("{{ route('dashboard.orders.export') }}");
            url.searchParams.append('limit', limit);
            url.searchParams.append('status', status);

            window.location.href = url.toString();
            document.getElementById("orderLimit").value = '';
        }

        function exportOrdersSuccess() {
            let limit = document.getElementById("orderSuccessLimit").value || 10;
            let status = $('#orderStatus').val();

            if (!status) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'يرجى تحديد نوع الطلب أولاً.',
                    icon: 'error',
                    confirmButtonText: 'حسنًا'
                });
                return;
            }

            let url = new URL("{{ route('dashboard.orders.export.success') }}");
            url.searchParams.append('limit', limit);
            url.searchParams.append('status', status);

            window.location.href = url.toString();
            document.getElementById("orderSuccessLimit").value = '';
        }
        function exportGroupedOrders() {
            let limit = document.getElementById("orderGroupedLimit").value || 10;
            let status = $('#orderStatus').val();

            if (!status) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'يرجى تحديد نوع الطلب أولاً.',
                    icon: 'error',
                    confirmButtonText: 'حسنًا'
                });
                return;
            }

            let url = new URL("{{ route('dashboard.orders.export.grouped') }}");
            url.searchParams.append('limit', limit);
            url.searchParams.append('status', status);

            window.location.href = url.toString();
            document.getElementById("orderGroupedLimit").value = '';
        }

        function exportBranchOrders() {
            let limit = document.getElementById("orderBranchLimit").value || 10;
            let status = $('#orderStatus').val();

            if (!status) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'يرجى تحديد نوع الطلب أولاً.',
                    icon: 'error',
                    confirmButtonText: 'حسنًا'
                });
                return;
            }

            let url = new URL("{{ route('dashboard.orders.export.branch') }}");
            url.searchParams.append('limit', limit);
            url.searchParams.append('status', status);

            window.location.href = url.toString();
            document.getElementById("orderBranchLimit").value = '';
        }

        function validateInput(input) {
            if (input.value < 1) {
                input.value = 1;
            }
        }
    </script>
@endsection
