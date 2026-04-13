@extends('admin.structure.app')

@section('title', 'Batch Details: ' . $batch->batch_number)

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Batch Details: {{ $batch->batch_number }}</h4>
        <a href="{{ route('admin.inventory.batches.index') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
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
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-center">Received Qty</th>
                                    <th class="text-center text-danger">Damaged Qty</th>
                                    <th>Serial Numbers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $gs = \App\HelperClass::generalSettings(); @endphp
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
                                        <td class="text-end">
                                            {{ $gs->currency ?? '$' }}{{ number_format($bp->unit_cost ?? 0, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-dark">{{ $bp->received_qty }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-danger fw-bold">{{ $bp->damaged_qty }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $itemSerials = $batch->serials->where('product_id', $bp->product_id)->where('product_variant_id', $bp->product_variant_id);
                                                $serialCount = $itemSerials->count();
                                            @endphp
                                            @if($serialCount > 0)
                                                <button type="button" class="btn btn-soft-primary btn-sm view-serials-btn" 
                                                        data-product="{{ $bp->product->name }}"
                                                        data-serials="{{ $itemSerials->values()->toJson() }}">
                                                    View ({{ $serialCount }})
                                                </button>
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

<!-- Serials Modal -->
<div class="modal fade" id="serialsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tracked Serials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Product:</label>
                    <div id="modalProductName" class="fw-bold"></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Serial No.</th>
                                <th>Status</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody id="serialsList">
                            <!-- Serials added via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.view-serials-btn').on('click', function() {
        const productName = $(this).data('product');
        const serials = $(this).data('serials');
        const listContainer = $('#serialsList');
        
        $('#modalProductName').text(productName);
        listContainer.empty();
        
        serials.forEach(serial => {
            let pBadgeClass = '';
            switch(serial.product_status) {
                case 'good': pBadgeClass = 'badge-soft-success'; break;
                case 'damaged': pBadgeClass = 'badge-soft-danger'; break;
                case 'damaged_return': pBadgeClass = 'badge-soft-warning'; break;
                default: pBadgeClass = 'badge-soft-secondary';
            }

            let sBadgeClass = serial.stock_status === 'in_stock' ? 'badge-soft-info' : 'badge-soft-dark';
            let sLabel = serial.stock_status === 'in_stock' ? 'In Stock' : 'Sold';

            listContainer.append(`
                <tr>
                    <td><code>${serial.serial_no}</code></td>
                    <td><span class="badge ${pBadgeClass}">${serial.product_status.replace('_', ' ').toUpperCase()}</span></td>
                    <td><span class="badge ${sBadgeClass}">${sLabel}</span></td>
                </tr>
            `);
        });
        
        $('#serialsModal').modal('show');
    });
});
</script>
@endsection
