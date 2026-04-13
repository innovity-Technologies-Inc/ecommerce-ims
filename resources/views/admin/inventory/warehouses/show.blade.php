@extends('admin.structure.app')

@section('title', 'Warehouse Stock Details')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Warehouse: {{ $warehouse->name }}</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Stock Inventory Breakdown</h5>
                    <span class="badge bg-success">
                        Active Warehouse
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Batch No</th>
                                    <th>Product / Variant</th>
                                    <th class="text-center">Current Qty</th>
                                    <th class="text-center">Damaged Qty</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warehouse->inventoryLevels as $index => $level)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($level->batch)
                                                <code>{{ $level->batch->batch_number }}</code>
                                            @else
                                                <span class="text-muted small">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.products.show', $level->product_id) }}" class="fw-bold text-primary">
                                                {{ $level->product->name }}
                                            </a>
                                            @if($level->variant)
                                                <br><small class="text-muted">Variant: {{ $level->variant->variant_name }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold fs-14 {{ $level->current_quantity <= ($level->min_stock_override ?? 0) ? 'text-danger' : 'text-success' }}">
                                                {{ $level->current_quantity }}
                                            </span>
                                        </td>
                                        <td class="text-center text-danger fw-bold">
                                            {{ $level->damaged_quantity }}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $serials = \App\Models\BatchSerial::where('warehouse_id', $warehouse->id)
                                                    ->where('product_id', $level->product_id)
                                                    ->where('product_variant_id', $level->product_variant_id)
                                                    ->where('batch_id', $level->batch_id)
                                                    ->get();
                                            @endphp
                                            
                                            @if($serials->count() > 0)
                                                <button type="button" class="btn btn-soft-primary btn-sm view-serials-btn" 
                                                        data-product="{{ $level->product->name }} {{ $level->variant ? '(' . $level->variant->variant_name . ')' : '' }}"
                                                        data-serials='@json($serials->values())'>
                                                    <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> View Serials ({{ $serials->count() }})
                                                </button>
                                            @else
                                                <span class="text-muted small">No Serials</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center p-4">
                                            <p class="text-muted">No stock records found for this warehouse.</p>
                                        </td>
                                    </tr>
                                @endforelse
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
                <h5 class="modal-title">Physical Units (Serials)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Product:</label>
                    <div id="modalProductName" class="fw-bold"></div>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light sticky-top">
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
