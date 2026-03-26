@extends('admin.structure.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Purchase Order: {{ $po->po_number }}</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.inventory.po.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                    @if($po->status === 'Sent')
                        @can('po.edit')
                        <a href="{{ route('admin.inventory.po.receive', $po->id) }}" class="btn btn-success btn-sm">
                            <iconify-icon icon="solar:box-minimalistic-bold-duotone" class="align-middle fs-18 me-1"></iconify-icon> Receive PO
                        </a>
                        @endcan
                    @endif
                    @if($po->status === 'Draft')
                        @can('po.edit')
                        <a href="{{ route('admin.inventory.po.edit', $po->id) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-edit me-1"></i> Edit PO
                        </a>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Ordered Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product / Variant</th>
                                    <th class="text-center">Ordered Qty</th>
                                    <th class="text-center">Received Qty</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product->name }}
                                            @if($item->variant)
                                                <br><small class="text-muted">Variant: {{ $item->variant->variant_name }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center"><span class="badge badge-soft-primary fs-13">{{ $item->order_quantity }}</span></td>
                                        <td class="text-center"><span class="badge badge-soft-success fs-13">{{ $item->received_quantity }}</span></td>
                                        <td class="text-end">{{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Estimated Total Amount:</th>
                                    <th class="text-end">{{ number_format($po->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($po->batches->count() > 0)
                        <h5 class="card-title mt-4 mb-3">Received Batches & Inventory</h5>
                        @foreach($po->batches->load(['warehouse', 'items.product', 'items.variant', 'serials']) as $batch)
                            <div class="card border mb-3">
                                <div class="card-header bg-light-subtle d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold">Batch: <code>{{ $batch->batch_number }}</code></span>
                                        <span class="ms-3 badge {{ $batch->warehouse->is_quarantine ? 'bg-danger' : 'bg-success' }}">
                                            Warehouse: {{ $batch->warehouse->name }}
                                        </span>
                                    </div>
                                    <small class="text-muted">{{ $batch->created_at->format('M d, Y H:i') }}</small>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0 align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th class="text-center" style="width: 80px;">Qty</th>
                                                    <th>Serials</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($batch->items as $item)
                                                    <tr>
                                                        <td class="ps-3">
                                                            {{ $item->product->name }}
                                                            @if($item->variant)
                                                                <small class="text-muted">({{ $item->variant->variant_name }})</small>
                                                            @endif
                                                        </td>
                                                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                                        <td>
                                                            @php
                                                                $itemSerials = $batch->serials->where('product_id', $item->product_id)->where('product_variant_id', $item->product_variant_id);
                                                            @endphp
                                                            @if($itemSerials->count() > 0)
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @foreach($itemSerials->take(10) as $serial)
                                                                        <span class="badge badge-soft-secondary">{{ $serial->serial_no }}</span>
                                                                    @endforeach
                                                                    @if($itemSerials->count() > 10)
                                                                        <span class="badge badge-soft-secondary">+{{ $itemSerials->count() - 10 }} more</span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted small italic">No serials</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($po->notes)
                        <div class="mt-4">
                            <h6>Notes:</h6>
                            <p class="text-muted">{{ $po->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Summary</h5>
                    
                    <div class="mb-3">
                        <label class="text-muted mb-1">Status:</label>
                        <div>
                            @php
                                $badgeClass = match($po->status) {
                                    'Draft' => 'badge-soft-secondary',
                                    'Sent' => 'badge-soft-info',
                                    'Delivered' => 'badge-soft-success',
                                    default => 'badge-soft-dark'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-14">{{ $po->status }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Supplier:</label>
                        <div class="fw-bold">{{ $po->supplier->name }}</div>
                        <div class="text-muted small">{{ $po->supplier->email }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Target Warehouse:</label>
                        <div class="fw-bold text-primary">{{ $po->warehouse->name ?? 'Not Set' }}</div>
                        <div class="text-muted small">{{ $po->warehouse->location ?? '' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Order Date:</label>
                        <div class="fw-bold">{{ $po->order_date->format('M d, Y') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Expected Delivery:</label>
                        <div class="fw-bold text-info">{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'N/A' }}</div>
                    </div>

                    @if($po->received_date)
                        <div class="mb-3">
                            <label class="text-muted mb-1">Received Date:</label>
                            <div class="fw-bold text-success">{{ $po->received_date->format('M d, Y') }}</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="text-muted mb-1">Created By:</label>
                        <div class="fw-bold">{{ $po->creator->name ?? 'System' }}</div>
                    </div>

                    @if($po->status === 'Draft')
                    <hr>
                    <form action="{{ route('admin.inventory.po.update-status', $po->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="statusUpdate" class="form-label">Update Status</label>
                            <select name="status" id="statusUpdate" class="form-select">
                                <option value="Draft" selected>Draft</option>
                                <option value="Sent">Sent</option>
                            </select>
                        </div>

                        <div id="notifySupplierContainer" class="mb-3" style="display: none;">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="notify_supplier" id="notifyUpdate" value="1" {{ $po->notify_supplier ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifyUpdate">Notify Supplier by Email</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-soft-success w-100 mt-2">Update Status</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#statusUpdate').change(function() {
        const status = $(this).val();

        if (status === 'Sent') {
            $('#notifySupplierContainer').show();
        } else {
            $('#notifySupplierContainer').hide();
        }
    });

    // Trigger on load
    $('#statusUpdate').trigger('change');
});
</script>
@endsection
