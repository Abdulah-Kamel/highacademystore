@extends('admin.layouts.master')
@section('title')
    shipping methods
@endsection
@section('content')
    <style>
        .shipping-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            height: 200px;
            display: flex;
            align-items: center;
        }
        .shipping-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            border-color: #007bff;
        }
        .shipping-card .card-body {
            text-align: center;
            padding: 2rem;
        }
        .shipping-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .page-title {
            text-align: center;
            margin-bottom: 3rem;
            color: #495057;
        }
    </style>
    
    <div class="container-fluid my-5">
        <div class="row">
            <div class="col-12">
                <h2 class="page-title">اختر طريقة الشحن لعرض الطلبات</h2>
            </div>
        </div>
        
        <!-- Shipping Method Cards -->
        <div class="row justify-content-center">
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card shipping-card" onclick="window.location.href='{{ route('dashboard.orders.barcode.list') }}?shipping=all'">
                    <div class="card-body">
                        <i class="fa fa-list shipping-icon text-primary"></i>
                        <h4 class="card-title">جميع الطلبات</h4>
                        <p class="card-text text-muted">عرض كافة الطلبات بجميع طرق الشحن</p>
                    </div>
                </div>
            </div>
            
            @php
                $shippingTypes = \App\Models\ShippingMethod::select('type')->distinct()->get();
                $typeLabels = [
                    'branch' => 'استلام من الفرع',
                    'post' => 'البريد المصري',
                    'home' => 'التوصيل للمنزل'
                ];
                $typeIcons = [
                    'branch' => 'fa-building',
                    'post' => 'fa-envelope',
                    'home' => 'fa-home'
                ];
            @endphp
            
            @foreach($shippingTypes as $type)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card shipping-card" onclick="window.location.href='{{ route('dashboard.orders.barcode.list') }}?shipping={{ $type->type }}'">
                    <div class="card-body">
                        <i class="fa {{ $typeIcons[$type->type] ?? 'fa-truck' }} shipping-icon text-success"></i>
                        <h4 class="card-title">{{ $typeLabels[$type->type] ?? $type->type }}</h4>
                        <p class="card-text text-muted">عرض طلبات {{ $typeLabels[$type->type] ?? $type->type }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection
