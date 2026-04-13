@extends('admin.structure.app')

@section('title', 'Damaged Product Details')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Damaged Product Details</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.inventory.damaged.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Damaged Product Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th style="width: 35%;">Batch Number:</th>
                            <td>
                                @if($level->batch)
                                    <code>{{ $level->batch->batch_number }}</code>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Product:</th>
                            <td>
                                <a href="{{ route('admin.products.show', $level->product_id) }}" class="fw-bold">
                                    {{ $level->product->name }}
                                </a>
                            </td>
                        </tr>
                        @if($level->variant)
                        <tr>
                            <th>Variant:</th>
                            <td><span class="badge badge-soft-secondary">{{ $level->variant->variant_name }}</span></td>
                        </tr>
                        @endif
                        <tr>
                            <th>Warehouse:</th>
                            <td>
                                <span class="badge bg-danger">
                                    {{ $level->warehouse->name }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Damaged Quantity:</th>
                            <td><span class="fw-bold fs-16 text-danger">{{ $level->damaged_quantity }}</span></td>
                        </tr>
                        <tr>
                            <th>Last Entry:</th>
                            <td>{{ $level->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Damaged Serials</h5>
                </div>
                <div class="card-body">
                    @php
                        $serials = \App\Models\BatchSerial::where('batch_id', $level->batch_id)
                            ->where('product_id', $level->product_id)
                            ->where('product_variant_id', $level->product_variant_id)
                            ->where('warehouse_id', $level->warehouse_id)
                            ->where('product_status', 'damaged')
                            ->where('stock_status', 'in_stock')
                            ->get();
                    @endphp
                    
                    @if($serials->count() > 0)
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Serial Number</th>
                                        <th>Condition</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serials as $index => $serial)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><code>{{ $serial->serial_no }}</code></td>
                                            <td><span class="badge badge-soft-danger">DAMAGED</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mt-2">Showing {{ $serials->count() }} damaged serial numbers.</p>
                    @else
                        <div class="text-center py-4">
                            <iconify-icon icon="solar:info-circle-broken" class="fs-32 text-muted mb-2"></iconify-icon>
                            <p class="text-muted small">No specific serial numbers are currently tracked as damaged for this record.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
