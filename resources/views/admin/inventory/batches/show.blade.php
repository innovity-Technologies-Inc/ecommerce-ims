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
                        <label class="text-muted mb-1">Supplier:</label>
                        <div class="fw-bold">{{ $batch->supplier->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Warehouse:</label>
                        <div>
                            <span class="badge badge-soft-info fs-13">
                                {{ $batch->warehouse->name }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Total Received:</label>
                        <div class="fw-bold fs-16">{{ $batch->total_received_qty }} Units</div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="text-muted small mb-1">Saleable:</label>
                            <div class="text-success fw-bold">{{ $batch->total_saleable_qty }}</div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small mb-1">Damaged:</label>
                            <div class="text-danger fw-bold">{{ $batch->total_damaged_qty }}</div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted mb-1">Received At:</label>
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
                                    <th class="text-center">Rec.</th>
                                    <th class="text-center">Good</th>
                                    <th class="text-center text-danger">Dmg.</th>
                                    <th>Serial Numbers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($batch->batchProducts as $bp)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.products.show', $bp->product_id) }}" class="fw-bold">
                                                {{ $bp->product->name }}
                                            </a>
                                            @if($bp->variant)
                                                <br><small class="text-muted">Variant: {{ $bp->variant->variant_name }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-dark">{{ $bp->received_qty }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-success fw-bold">{{ $bp->saleable_qty }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-danger fw-bold">{{ $bp->damaged_qty }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $itemSerials = $batch->serials->where('product_id', $bp->product_id)->where('product_variant_id', $bp->product_variant_id);
                                            @endphp
                                            @if($itemSerials->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($itemSerials as $serial)
                                                        @php
                                                            $badgeClass = match($serial->product_status) {
                                                                'good' => 'badge-soft-success',
                                                                'damaged' => 'badge-soft-danger',
                                                                'damaged_return' => 'badge-soft-warning',
                                                                default => 'badge-soft-secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}" title="Status: {{ ucfirst($serial->product_status) }}">
                                                            {{ $serial->serial_no }}
                                                        </span>
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
