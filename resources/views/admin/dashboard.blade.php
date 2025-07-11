@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/chart.js/Chart.min.css') }}">
@endpush

@push('page-header')
<div class="col-sm-12">
    <h3 class="page-title">Welcome {{ auth()->user()->name }}!</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>
    </ul>
</div>
@endpush

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-success border-success">
                        <i class="fe fe-money"></i>
                    </span>
                    <div class="dash-count">
                        <h3>{{ AppSettings::get('app_currency', '$') }} {{ $today_sales }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted">Today's Sales</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-info">
                        <i class="fa fa-th-large"></i>
                    </span>
                    <div class="dash-count">
                        <h3>{{ $total_categories }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted">Available Categories</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-danger border-danger">
                        <i class="fe fe-folder"></i>
                    </span>
                    <div class="dash-count">
                        <h3>{{ $total_expired_products }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted">Expired Medicines</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-warning border-warning">
                        <i class="fe fe-users"></i>
                    </span>
                    <div class="dash-count">
                        <h3>{{ \DB::table('users')->count() }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted">System Users</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popular Products Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-table p-3">
            <div class="card-header">
                <h4 class="card-title">Popular Products (Last 7 Days)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Total Quantity Sold</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($popularSales as $sale)
                            <tr>
                                <td>{{ $sale->product_name }}</td>
                                <td>{{ $sale->total_quantity }}</td>
                                <td>{{ number_format($sale->total_sales, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No popular sales data found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Sales List -->
    <div class="col-md-12 col-lg-7">
        <div class="card card-table p-3">
            <div class="card-header">
                <h4 class="card-title">Recent Sales List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sales-table" class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="col-md-12 col-lg-5">
        <!-- Pie Chart -->
        <div class="card card-chart">
            <div class="card-header">
                <h4 class="card-title text-center">Graph Report</h4>
            </div>
            <div class="card-body">
                {!! $pieChart->render() !!}
            </div>
        </div>

@endsection

@push('page-js')
<script src="{{ asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sales.index') }}",
            columns: [
                { data: 'product', name: 'product' },
                { data: 'quantity', name: 'quantity' },
                { data: 'total_price', name: 'total_price' },
                { data: 'date', name: 'date' },
            ]
        });
    });

</script>
@endpush
