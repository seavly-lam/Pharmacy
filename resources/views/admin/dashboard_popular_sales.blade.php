@extends('admin.layouts.app')

@push('page-header')
<div class="col-sm-12">
    <h3 class="page-title">Popular Medicine Sales (Last 7 Days)</h3>
</div>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        @if($popularSales->isEmpty())
            <p>No sales data found for the last 7 days.</p>
        @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Total Quantity Sold</th>
                    <th>Total Sales ({{ AppSettings::get('app_currency', '$') }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach($popularSales as $sale)
                <tr>
                    <td>{{ $sale->product_name }}</td>
                    <td>{{ $sale->total_quantity }}</td>
                    <td>{{ number_format($sale->total_sales, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
