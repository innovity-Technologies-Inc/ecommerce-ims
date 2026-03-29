@extends('admin.structure.app')

@section('title', 'Batch Details: ' . $batch->batch_number)

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Batch Details: {{ $batch->batch_number }}</h4>
                <div class="page-title-right">
                    <a href="{{ $batch->warehouse->is_quarantine ? route('admin.inventory.damaged.index') : route('admin.inventory.stock.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back to Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Batch Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted mb-1">Batch Number:</label>
                        <div class="fw-bold"><code>{{ $batch->batch_number }}</code></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Purchase Order:</label>
                        <div>
                            @if($batch->purchaseOrder)
                                <a href="{{ route('admin.inventory.po.show', $batch->purchase_order_id) }}" class="fw-bold">
                                    {{ $batch->purchaseOrder->po_number }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Warehouse:</label>
                        <div>
                            <span class="badge {{ $batch->warehouse->is_quarantine ? 'bg-danger' : 'bg-success' }} fs-13">
                                {{ $batch->warehouse->name }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Physical Location:</label>
                        <div class="text-muted">{{ $batch->warehouse->location ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted mb-1">Created At:</label>
                        <div class="fw-bold">{{ $batch->created_at->format('M d, Y H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Products in this Batch</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product / Variant</th>
                                    <th class="text-center">Quantity</th>
                                    <th>Serial Numbers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($batch->items as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.products.show', $item->product_id) }}" class="fw-bold">
                                                {{ $item->product->name }}
                                            </a>
                                            @if($item->variant)
                                                <br><small class="text-muted">Variant: {{ $item->variant->variant_name }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-dark fs-14">{{ $item->quantity }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $itemSerials = $batch->serials->where('product_id', $item->product_id)->where('product_variant_id', $item->product_variant_id);
                                            @endphp
                                            @if($itemSerials->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($itemSerials as $serial)
                                                        <span class="badge {{ $serial->status === 'Damaged' ? 'badge-soft-danger' : 'badge-soft-secondary' }}">{{ $serial->serial_no }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted small italic">No serials tracked</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
