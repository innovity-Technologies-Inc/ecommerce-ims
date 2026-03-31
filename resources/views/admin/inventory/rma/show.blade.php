@extends('admin.structure.app')

@section('title', 'Supplier RMA Details')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">RMA Details: {{ $rma->rma_number }}</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.inventory.rma.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Returned Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product / Batch</th>
                                    <th class="text-center">Quantity</th>
                                    <th>Serials</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rma->rmaItems->groupBy(fn($item) => $item->batch_id . '-' . $item->product_id . '-' . ($item->product_variant_id ?? '')) as $group)
                                    @php 
                                        $first = $group->first();
                                        $serials = $group->pluck('serial')->filter();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ $first->product->name }}</div>
                                            @if($first->variant)
                                                <small class="text-muted">Variant: {{ $first->variant->variant_name }}</small><br>
                                            @endif
                                            <small class="text-primary">Batch: {{ $first->batch->batch_number }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-primary fs-13">{{ $group->sum('quantity') }}</span>
                                        </td>
                                        <td>
                                            @if($serials->count() > 0)
                                                <button type="button" class="btn btn-soft-primary btn-sm view-serials-btn" 
                                                        data-product="{{ $first->product->name }} {{ $first->variant ? '(' . $first->variant->variant_name . ')' : '' }}"
                                                        data-serials='@json($serials->values())'>
                                                    <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> View ({{ $serials->count() }})
                                                </button>
                                            @else
                                                <span class="text-muted small italic">No serials</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($rma->remarks)
                        <div class="mt-4">
                            <h6>Remarks:</h6>
                            <p class="text-muted">{{ $rma->remarks }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-4">RMA Summary</h5>
                    
                    <div class="mb-3">
                        <label class="text-muted mb-1">Status:</label>
                        <div>
                            @php
                                $badgeClass = match($rma->status) {
                                    'pending' => 'badge-soft-warning',
                                    'approved' => 'badge-soft-info',
                                    'shipped' => 'badge-soft-primary',
                                    'closed' => 'badge-soft-success',
                                    default => 'badge-soft-dark'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-uppercase fs-14">{{ $rma->status }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Supplier:</label>
                        <div class="fw-bold">{{ $rma->supplier->name }}</div>
                        <div class="text-muted small">{{ $rma->supplier->email }}</div>
                    </div>

                    @if($rma->purchaseOrder)
                        <div class="mb-3">
                            <label class="text-muted mb-1">Related PO:</label>
                            <div class="fw-bold text-primary">{{ $rma->purchaseOrder->po_number }}</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="text-muted mb-1">Creation Date:</label>
                        <div class="fw-bold">{{ $rma->created_at->format('M d, Y H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">{{ $rma->status === 'closed' ? 'Completed Date:' : 'Last Updated:' }}</label>
                        <div class="fw-bold {{ $rma->status === 'closed' ? 'text-success' : '' }}">{{ $rma->updated_at->format('M d, Y H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Notify Supplier:</label>
                        <div>
                            <span class="badge {{ $rma->notify_supplier ? 'bg-success' : 'bg-secondary' }}">
                                {{ $rma->notify_supplier ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($rma->status !== 'closed')
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Update Status</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.inventory.rma.update-status', $rma->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Next Status</label>
                                <select name="status" class="form-select" required>
                                    @if($rma->status === 'pending')
                                        <option value="approved">Approved</option>
                                    @elseif($rma->status === 'approved')
                                        <option value="shipped">Shipped</option>
                                    @elseif($rma->status === 'shipped')
                                        <option value="closed">Closed (Update Inventory)</option>
                                    @endif
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary {{ $rma->status === 'shipped' ? 'confirmAction' : '' }}" 
                                    data-confirm-title="Close RMA"
                                    data-confirm-text="Are you sure you want to CLOSE this RMA? This will update your inventory levels and ledger."
                                    data-confirm-icon="warning">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-success border-0 mb-0" role="alert">
                    <strong>Completed!</strong> This RMA has been closed and inventory has been adjusted.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Serials Modal -->
<div class="modal fade" id="serialsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Returned Serials</h5>
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
