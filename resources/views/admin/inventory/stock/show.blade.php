@extends('admin.structure.app')

@section('title', 'Product Stock Details')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Product Stock Details</h4>
                <div class="page-title-right">
                    <a href="{{ $level->warehouse->is_quarantine ? route('admin.inventory.damaged.index') : route('admin.inventory.stock.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product & Warehouse Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th style="width: 30%;">Product:</th>
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
                        <tr>
                            <th>SKU:</th>
                            <td><code>{{ $level->variant->sku }}</code></td>
                        </tr>
                        @endif
                        <tr>
                            <th>Warehouse:</th>
                            <td>
                                <span class="badge {{ $level->warehouse->is_quarantine ? 'bg-danger' : 'bg-success' }}">
                                    {{ $level->warehouse->name }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Location:</th>
                            <td class="text-muted">{{ $level->warehouse->location ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Current Stock:</th>
                            <td><span class="fw-bold fs-16 {{ $level->current_quantity <= ($level->min_stock_override ?? 0) ? 'text-danger' : 'text-success' }}">{{ $level->current_quantity }}</span></td>
                        </tr>
                        <tr>
                            <th>Min Alert Level:</th>
                            <td>{{ $level->min_stock_override ?? 'No override' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Procurement & Batch Info</h5>
                </div>
                <div class="card-body">
                    @if($level->batch)
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th style="width: 30%;">Batch Number:</th>
                            <td><code>{{ $level->batch->batch_number }}</code></td>
                        </tr>
                        <tr>
                            <th>Supplier:</th>
                            <td>{{ $level->batch->supplier->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Purchase Order:</th>
                            <td>
                                @if($level->batch->purchaseOrder)
                                    <a href="{{ route('admin.inventory.po.show', $level->batch->purchase_order_id) }}">
                                        {{ $level->batch->purchaseOrder->po_number }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Received Date:</th>
                            <td>{{ $level->batch->created_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                    
                    <h6 class="mt-4 mb-2">Tracked Serials (In this record)</h6>
                    @php
                        $serials = \App\Models\BatchSerial::where('batch_id', $level->batch_id)
                            ->where('product_id', $level->product_id)
                            ->where('product_variant_id', $level->product_variant_id)
                            ->where('warehouse_id', $level->warehouse_id)
                            ->get();
                    @endphp
                    @if($serials->count() > 0)
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($serials as $serial)
                                <span class="badge {{ $serial->status === 'damaged' ? 'badge-soft-danger' : 'badge-soft-info' }}">{{ $serial->serial_no }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small italic">No serial numbers tracked for this specific inventory record.</p>
                    @endif

                    @else
                    <div class="text-center py-4">
                        <iconify-icon icon="solar:info-circle-broken" class="fs-32 text-muted mb-2"></iconify-icon>
                        <p class="text-muted">This stock was allocated manually or has no linked batch.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
