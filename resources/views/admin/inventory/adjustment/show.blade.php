@extends('admin.structure.app')

@section('title', 'Stock Adjustment Details')

@section('content')
    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Adjustment Details: {{ $adjustment->adjustment_number }}</h4>
            <a href="{{ route('admin.inventory.adjustment.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Adjustment Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#SL</th>
                                        <th>Product / Variant</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end">Total Cost</th>
                                        <th class="text-center">Serials</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($adjustment->items as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-medium">{{ $item->product->name }}</div>
                                                @if($item->variant)
                                                    <small class="text-muted">Variant: {{ $item->variant->variant_name }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($item->unit_cost, 2) }}</td>
                                            <td class="text-end">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($item->quantity * $item->unit_cost, 2) }}</td>
                                            <td class="text-center">
                                                @if($item->serials && $item->serials->count() > 0)
                                                    <button type="button" class="btn btn-soft-primary btn-sm view-serials-btn" 
                                                            data-product="{{ $item->product->name }} {{ $item->variant ? '(' . $item->variant->variant_name . ')' : '' }}"
                                                            data-serials='@json($item->serials)'>
                                                        <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> View ({{ $item->serials->count() }})
                                                    </button>
                                                @else
                                                    <span class="text-muted small italic">No serials</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Grand Totals:</th>
                                        <th class="text-center">{{ $adjustment->items->sum('quantity') }}</th>
                                        <th></th>
                                        <th class="text-end">
                                            @php 
                                                $totalCost = $adjustment->items->sum(fn($i) => $i->quantity * $i->unit_cost);
                                            @endphp
                                            {{ number_format($totalCost, 2) }}
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                @if($adjustment->remarks)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Remarks</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $adjustment->remarks }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted mb-1">Adjustment Number:</label>
                            <div class="fw-bold">{{ $adjustment->adjustment_number }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Warehouse:</label>
                            <div class="fw-bold">{{ $adjustment->warehouse->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Batch Number:</label>
                            <div class="fw-bold text-primary">{{ $adjustment->batch->batch_number }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Adjustment Date:</label>
                            <div class="fw-bold">{{ $adjustment->adjustment_date->format('d M, Y') }}</div>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted mb-1">Created By:</label>
                            <div class="fw-bold">{{ $adjustment->creator->name }}</div>
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
                    <h5 class="modal-title">Adjusted Serials</h5>
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
                                    <th>Stock Status</th>
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

            let sBadgeClass = '';
            switch(serial.stock_status) {
                case 'in_stock': sBadgeClass = 'badge-soft-info'; break;
                case 'returned': sBadgeClass = 'badge-soft-success'; break;
                case 'sold': sBadgeClass = 'badge-soft-dark'; break;
                default: sBadgeClass = 'badge-soft-secondary';
            }

            listContainer.append(`
                <tr>
                    <td><code>${serial.serial_no}</code></td>
                    <td><span class="badge ${pBadgeClass}">${serial.product_status.replace('_', ' ').toUpperCase()}</span></td>
                    <td><span class="badge ${sBadgeClass}">${serial.stock_status.replace('_', ' ').toUpperCase()}</span></td>
                </tr>
            `);
        });
        
        $('#serialsModal').modal('show');
    });
});
</script>
@endsection
