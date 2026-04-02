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
                            <td><span class="fw-bold fs-16 text-success">{{ $level->current_quantity }}</span></td>
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
                        @php
                            $batchProduct = \App\Models\BatchProduct::where([
                                'batch_id' => $level->batch_id,
                                'product_id' => $level->product_id,
                                'product_variant_id' => $level->product_variant_id
                            ])->first();
                            $gs = \App\HelperClass::generalSettings();
                        @endphp
                        @if($batchProduct)
                        <tr>
                            <th>Unit Cost:</th>
                            <td class="fw-bold text-primary">{{ $gs->currency ?? '$' }}{{ number_format($batchProduct->unit_cost, 2) }}</td>
                        </tr>
                        @endif
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
                        <button type="button" class="btn btn-soft-primary btn-sm view-serials-btn" 
                                data-product="{{ $level->product->name }} {{ $level->variant ? '(' . $level->variant->variant_name . ')' : '' }}"
                                data-serials='@json($serials->values())'>
                            <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> View Serials ({{ $serials->count() }})
                        </button>
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
