@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-8">
                {{-- Order Items Table --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0 d-inline-block me-2">Order Details: {{ $order->order_id }}</h5>
                            @if($order->returnRequests->where('status', 'received')->count() > 0)
                                <span class="badge bg-soft-danger text-danger">
                                    <i class="bx bx-undo me-1"></i> Returned Items
                                </span>
                            @endif
                        </div>
                        <span class="text-muted">{{ $order->created_at->format('d M, Y h:i A') }}</span>
                    </div>
                    <div class="card-body">
                        @php
                            $returnedItemsMap = \App\Models\ReturnItem::whereIn('return_id', $order->returnRequests->pluck('id'))
                                ->where('is_received', true)
                                ->get()
                                ->groupBy(fn($ri) => $ri->product_id . '-' . ($ri->product_variant_id ?? '0'))
                                ->map(fn($group) => $group->sum('quantity'));
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-centered mb-0">
                                <thead class="bg-light-subtle">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->orderItems as $item)
                                    @php
                                        $key = $item->product_id . '-' . ($item->product_variant_id ?? '0');
                                        $returnedQty = $returnedItemsMap->get($key, 0);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                        @if($returnedQty > 0)
                                                            <span class="badge bg-soft-danger text-danger">
                                                                <i class="bx bx-undo me-1"></i> Returned: {{ $returnedQty }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($item->variant_name)
                                                        <small class="text-muted">{{ $item->variant_name }}</small>
                                                    @endif

                                                    {{-- Fulfillment Details --}}
                                                    @if($item->orderedProductBatches->count() > 0)
                                                        <div class="mt-2 pt-2 border-top border-dashed">
                                                            <div class="fw-bold small mb-1 text-muted">Fulfillment:</div>
                                                            @foreach($item->orderedProductBatches as $batch)
                                                                <div class="mb-2 last-child-mb-0">
                                                                    <div class="d-flex flex-wrap gap-2 mb-1">
                                                                        <span class="badge badge-soft-info" title="Warehouse">
                                                                            <i class="bx bx-home-alt me-1"></i> {{ $batch->batch->warehouse->name }}
                                                                        </span>
                                                                        <span class="badge badge-soft-secondary" title="Batch">
                                                                            <i class="bx bx-purchase-tag-alt me-1"></i> {{ $batch->batch->batch_number }}
                                                                        </span>
                                                                        <span class="badge badge-soft-dark">Qty: {{ $batch->quantity }}</span>
                                                                    </div>
                                                                    @php
                                                                        $itemBatchSerials = \App\Models\BatchSerial::where('order_item_id', $item->id)
                                                                            ->where('batch_id', $batch->batch_id)
                                                                            ->get();
                                                                    @endphp
                                                                    @if($itemBatchSerials->count() > 0)
                                                                        <div class="small ps-2 border-start border-2 border-info ms-1">
                                                                            <strong class="text-muted">Serials:</strong>
                                                                            <span class="text-primary">{{ $itemBatchSerials->pluck('serial_no')->implode(', ') }}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            ${{ number_format($item->regular_price, 2) }}
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            ${{ number_format($item->regular_price * $item->quantity, 2) }}
                                            @if($item->total_cost > 0)
                                                <div class="small text-muted mt-1" title="Procurement Cost">
                                                    Proc. Cost: ${{ number_format($item->total_cost, 2) }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row justify-content-end mt-3">
                            <div class="col-lg-5 col-sm-6">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                        <tr>
                                            <th>Subtotal :</th>
                                            <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shipping :</th>
                                            <td class="text-end">${{ number_format($order->shipping_charge, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Discount :</th>
                                            <td class="text-end">
                                                @if($order->coupon)
                                                    <span class="badge bg-soft-success text-success me-1">{{ $order->coupon->code }}</span>
                                                @endif
                                                ${{ number_format($order->discount, 2) }}
                                            </td>
                                        </tr>
                                        @if($order->total_cost > 0)
                                        <tr>
                                            <th>Total Cost :</th>
                                            <td class="text-end text-muted small">${{ number_format($order->total_cost, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="border-top">
                                            <th class="fs-16">Total :</th>
                                            <td class="text-end fs-16 fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Status Management (Moved from right to below items) --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Order Status & Fulfillment</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-6">
                                <label class="form-label d-block text-muted small text-uppercase fw-bold mb-1">Current Status</label>
                                @php
                                    $statusClass = match($order->order_status) {
                                        'Pending' => 'bg-warning',
                                        'Processing' => 'bg-info',
                                        'Shipped' => 'bg-primary',
                                        'Out for Delivery' => 'bg-dark',
                                        'Delivered' => 'bg-success',
                                        'Cancelled' => 'bg-danger',
                                        'Rejected' => 'bg-secondary',
                                        default => 'bg-dark'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} text-white fs-14 px-3 py-2">{{ $order->order_status }}</span>
                            </div>
                        </div>

                        <hr>

                        @if(empty($availableStatuses))
                            <div class="alert {{ $order->order_status === 'Delivered' ? 'alert-soft-success' : 'alert-soft-danger' }} border-0 mb-3" role="alert">
                                <i class="bx {{ $order->order_status === 'Delivered' ? 'bx-check-circle' : 'bx-info-circle' }} me-1"></i> This order is <strong>{{ $order->order_status }}</strong>. The status cannot be changed further.
                            </div>
                            @if($order->rejection_reason)
                                <div class="mb-3">
                                    <label class="form-label d-block text-muted small text-uppercase fw-bold">Reason/Remarks</label>
                                    <div class="p-2 bg-light rounded border">
                                        {{ $order->rejection_reason }}
                                    </div>
                                </div>
                            @endif
                        @else
                            @can('orders.edit')
                            <form id="statusUpdateForm" action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order_status" class="form-label">Update Status</label>
                                            <select name="order_status" id="order_status_select" class="form-select" required>
                                                <option value="">Select Next Status</option>
                                                @foreach($availableStatuses as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="email_notify" id="email_notify" value="1">
                                            <label class="form-check-label fw-medium" for="email_notify">Email Notify Customer</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="inventory_allocation_section" class="d-none mt-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-3">Inventory Allocation</h6>
                                    
                                    @foreach($order->orderItems as $item)
                                        <div class="card border mb-3 shadow-none allocation-item-card" 
                                             data-item-id="{{ $item->id }}" 
                                             data-product-id="{{ $item->product_id }}" 
                                             data-variant-id="{{ $item->product_variant_id }}"
                                             data-target-qty="{{ $item->quantity }}"
                                             data-product-name="{{ $item->product_name }}">
                                            <div class="card-header bg-light-subtle py-2 d-flex justify-content-between align-items-center">
                                                <div class="fw-bold small text-dark">
                                                    {{ $item->product_name }} 
                                                    @if($item->variant_name) <small class="text-muted">({{ $item->variant_name }})</small> @endif
                                                    <span class="badge bg-soft-primary text-primary ms-2">Ordered Qty: {{ $item->quantity }}</span>
                                                </div>
                                                <div class="allocation-status small">
                                                    Allocated: <span class="current-allocated-qty fw-bold">0</span> / {{ $item->quantity }}
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="row mb-2">
                                                    <div class="col-md-12">
                                                        <label class="form-label small fw-bold text-muted mb-1">Select Warehouse <span class="text-danger">*</span></label>
                                                        <select name="items[{{ $item->id }}][warehouse_id]" class="form-select form-select-sm item-warehouse-select" data-product-id="{{ $item->product_id }}" data-variant-id="{{ $item->product_variant_id }}">
                                                            <option value="">Select Warehouse</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="allocation-rows-container mt-2">
                                                    {{-- Allocation rows will be added here --}}
                                                </div>
                                                <div class="mt-2 d-none batch-action-container">
                                                    <button type="button" class="btn btn-sm btn-soft-success add-allocation-row-btn">
                                                        <i class="bx bx-plus me-1"></i> Add Another Batch
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <hr>
                                </div>

                                <div class="mb-3 d-none" id="rejection_reason_wrapper">
                                    <label for="rejection_reason" class="form-label">Reason/Remarks <span class="text-danger">*</span></label>
                                    <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" placeholder="Enter reason for cancellation or rejection..."></textarea>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-4 py-2">Update Order Status</button>
                                </div>
                            </form>
                            @else
                            <div class="alert alert-info border-0 mb-0" role="alert">
                                <i class="bx bx-info-circle me-1"></i> You do not have permission to update order status.
                            </div>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Customer & Shipping Information (Moved from left to right) --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer & Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6>Customer Info</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $order->name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $order->email }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $order->mobile }}</p>
                        </div>
                        <div>
                            <h6>Shipping Address</h6>
                            <p class="mb-1">{{ $order->address }}</p>
                            <p class="mb-1">{{ $order->city }}, {{ $order->state }} {{ $order->zip }}</p>
                            <p class="mb-1">{{ $order->country }}</p>
                        </div>
                        @if($order->notes)
                            <div class="mt-3">
                                <h6>Order Notes</h6>
                                <div class="p-2 bg-light rounded">
                                    {{ $order->notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payment Details</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Method:</strong> {{ $order->payment_method }}</p>
                        <p class="mb-0"><strong>Status:</strong> 
                            <span class="badge {{ $order->payment_status === 'Paid' ? 'bg-success' : 'bg-warning' }}">{{ $order->payment_status }}</span>
                        </p>
                    </div>
                </div>

                {{-- Invoice --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Invoice</h5>
                    </div>
                    <div class="card-body">
                        @if($order->invoice_no)
                            <div class="mb-3">
                                <label class="form-label d-block text-muted small text-uppercase fw-bold">Invoice Number</label>
                                <span class="fw-bold fs-16">{{ $order->invoice_no }}</span>
                                <br>
                                <small class="text-muted">Generated on: {{ $order->invoice_date->format('d M, Y') }}</small>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.orders.view-invoice', $order->id) }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="bx bx-show me-1"></i> View / Print Invoice
                                </a>
                                @can('orders.edit')
                                <form action="{{ route('admin.orders.regenerate-invoice', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-soft-secondary w-100">
                                        <i class="bx bx-refresh me-1"></i> Regenerate
                                    </button>
                                </form>
                                @endcan
                            </div>
                        @else
                            <div class="text-center py-2">
                                <p class="text-muted mb-3">No invoice generated yet.</p>
                                @can('orders.edit')
                                <form action="{{ route('admin.orders.generate-invoice', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bx bx-receipt me-1"></i> Generate Invoice
                                    </button>
                                </form>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Status History --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Status History</h5>
                    </div>
                    <div class="card-body">
                        @if($order->statusLogs->count() > 0)
                            <div class="timeline-wrapper">
                                @foreach($order->statusLogs as $log)
                                    <div class="d-flex mb-3">
                                        <div class="flex-shrink-0 me-3">
                                            @php
                                                $logClass = match($log->status) {
                                                    'Pending' => 'bg-warning',
                                                    'Processing' => 'bg-info',
                                                    'Shipped' => 'bg-primary',
                                                    'Out for Delivery' => 'bg-dark',
                                                    'Delivered' => 'bg-success',
                                                    'Cancelled' => 'bg-danger',
                                                    'Rejected' => 'bg-secondary',
                                                    default => 'bg-dark'
                                                };
                                            @endphp
                                            <div class="avatar-xs">
                                                <span class="avatar-title rounded-circle {{ $logClass }} shadow">
                                                    <i class="bx bx-check fs-12"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fs-14 fw-bold text-dark">{{ $log->status }}</h6>
                                            <p class="text-muted mb-0 small">
                                                <i class="bx bx-time-five me-1"></i> {{ $log->changed_at->format('d M, Y - h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">No history available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Serial Selection Modal --}}
    <div class="modal fade" id="serialSelectionModal" tabindex="-1" aria-labelledby="serialSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="serialSelectionModalLabel">Select Serials for <span id="modal-product-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-soft-info border-0 mb-3">
                        Please select exactly <strong id="modal-target-qty"></strong> serials. Currently selected: <strong id="modal-current-count">0</strong>
                    </div>
                    <div id="modal-serial-list" class="row">
                        {{-- Serials will be populated here --}}
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSerials">Confirm Selection</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const statusSelect = $('#order_status_select');
        const reasonWrapper = $('#rejection_reason_wrapper');
        const reasonInput = $('#rejection_reason');
        const allocationSection = $('#inventory_allocation_section');
        
        let currentItemContext = null; // { itemId, rowId, targetQty, productName }
        let availableSerials = {}; // { rowUniqueId: [ {id, serial_no} ] }
        let selectedSerials = {}; // { rowUniqueId: [id1, id2] }

        function toggleStatusFields() {
            const status = statusSelect.val();
            
            if (status === 'Cancelled' || status === 'Rejected') {
                reasonWrapper.removeClass('d-none');
                reasonInput.attr('required', 'required');
            } else {
                reasonWrapper.addClass('d-none');
                reasonInput.removeAttr('required');
            }

            if (status === 'Shipped') {
                allocationSection.removeClass('d-none');
                initializeWarehouses();
            } else {
                allocationSection.addClass('d-none');
            }
        }

        function initializeWarehouses() {
            $('.item-warehouse-select').each(function() {
                const select = $(this);
                const productId = select.data('product-id');
                const variantId = select.data('variant-id');

                if (select.find('option').length <= 1) {
                    select.prop('disabled', true).empty().append('<option value="">Loading warehouses...</option>');
                    $.ajax({
                        url: "{{ route('admin.orders.ajax.get-warehouses') }}",
                        data: { product_id: productId, variant_id: variantId },
                        success: function(data) {
                            select.prop('disabled', false).empty().append('<option value="">Select Warehouse</option>');
                            data.forEach(w => {
                                select.append(`<option value="${w.id}">${w.name}</option>`);
                            });
                        }
                    });
                }
            });
        }

        $(document).on('change', '.item-warehouse-select', function() {
            const select = $(this);
            const card = select.closest('.allocation-item-card');
            const warehouseId = select.val();
            const container = card.find('.allocation-rows-container');
            const actionContainer = card.find('.batch-action-container');

            // Clear existing rows
            container.find('.allocation-row').each(function() {
                const rowId = $(this).data('row-id');
                delete availableSerials[rowId];
                delete selectedSerials[rowId];
            });
            container.empty();
            actionContainer.addClass('d-none');

            if (warehouseId) {
                addAllocationRow(card, warehouseId);
                actionContainer.removeClass('d-none');
            }
            calculateAllocatedTotal(card);
        });

        function addAllocationRow(card, warehouseId) {
            const itemId = card.data('item-id');
            const productId = card.data('product-id');
            const variantId = card.data('variant-id');
            const rowIndex = card.find('.allocation-row').length;
            const rowUniqueId = `row_${itemId}_${Date.now()}_${Math.floor(Math.random() * 1000)}`;

            const rowHtml = `
                <div class="allocation-row border rounded p-2 mb-2 bg-white" data-row-id="${rowUniqueId}">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <label class="small text-muted mb-1 d-block">Batch <span class="text-danger">*</span></label>
                            <select name="items[${itemId}][allocations][${rowUniqueId}][batch_id]" class="form-select form-select-sm batch-select" data-warehouse-id="${warehouseId}" required>
                                <option value="">Select Batch</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted mb-1 d-block">Qty <span class="text-danger">*</span></label>
                            <input type="number" name="items[${itemId}][allocations][${rowUniqueId}][quantity]" class="form-control form-control-sm allocation-qty" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-md-2 text-end pt-3">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn ${rowIndex === 0 ? 'd-none' : ''}">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="serial-container d-none mt-2 pt-2 border-top border-dashed">
                        <button type="button" class="btn btn-xs btn-soft-primary select-serials-btn" 
                                data-item-id="${itemId}" 
                                data-row-id="${rowUniqueId}">
                            <i class="bx bx-list-check me-1"></i> Select Serials
                        </button>
                        <div class="selected-serials-display mt-1 small text-primary fw-bold"></div>
                        <div class="selected-serials-inputs"></div>
                    </div>
                </div>
            `;

            const $row = $(rowHtml);
            card.find('.allocation-rows-container').append($row);
            
            // Load batches for this warehouse/product
            const batchSelect = $row.find('.batch-select');
            $.ajax({
                url: "{{ route('admin.orders.ajax.get-batches') }}",
                data: { warehouse_id: warehouseId, product_id: productId, variant_id: variantId },
                success: function(data) {
                    batchSelect.empty().append('<option value="">Select Batch</option>');
                    data.forEach(b => {
                        batchSelect.append(`<option value="${b.id}">${b.batch_number} (Avail: ${b.saleable_qty})</option>`);
                    });
                }
            });
        }

        $(document).on('click', '.add-allocation-row-btn', function() {
            const card = $(this).closest('.allocation-item-card');
            const warehouseId = card.find('.item-warehouse-select').val();
            if (warehouseId) {
                addAllocationRow(card, warehouseId);
            }
        });

        $(document).on('click', '.remove-row-btn', function() {
            const row = $(this).closest('.allocation-row');
            const card = row.closest('.allocation-item-card');
            const rowUniqueId = row.data('row-id');
            
            delete availableSerials[rowUniqueId];
            delete selectedSerials[rowUniqueId];
            row.remove();
            calculateAllocatedTotal(card);
        });

        $(document).on('change', '.batch-select', function() {
            const select = $(this);
            const batchId = select.val();
            const row = select.closest('.allocation-row');
            const card = row.closest('.allocation-item-card');
            const serialContainer = row.find('.serial-container');
            const rowUniqueId = row.data('row-id');
            
            const productId = card.data('product-id');
            const variantId = card.data('variant-id');

            if (batchId) {
                $.ajax({
                    url: "{{ route('admin.orders.ajax.get-serials') }}",
                    data: { batch_id: batchId, product_id: productId, variant_id: variantId },
                    success: function(data) {
                        if (data && data.length > 0) {
                            availableSerials[rowUniqueId] = data;
                            selectedSerials[rowUniqueId] = [];
                            serialContainer.removeClass('d-none');
                        } else {
                            availableSerials[rowUniqueId] = null;
                            serialContainer.addClass('d-none');
                        }
                    }
                });
            } else {
                serialContainer.addClass('d-none');
            }
        });

        $(document).on('input', '.allocation-qty', function() {
            calculateAllocatedTotal($(this).closest('.allocation-item-card'));
        });

        function calculateAllocatedTotal(card) {
            let total = 0;
            card.find('.allocation-qty').each(function() {
                total += parseInt($(this).val()) || 0;
            });
            card.find('.current-allocated-qty').text(total);
            
            const target = card.data('target-qty');
            if (total > target) {
                card.find('.current-allocated-qty').addClass('text-danger');
            } else {
                card.find('.current-allocated-qty').removeClass('text-danger');
            }
        }

        $(document).on('click', '.select-serials-btn', function() {
            const btn = $(this);
            const row = btn.closest('.allocation-row');
            const card = row.closest('.allocation-item-card');
            const qty = parseInt(row.find('.allocation-qty').val()) || 0;

            if (qty <= 0) {
                toastr.error('Please enter a valid quantity first.');
                return;
            }

            currentItemContext = {
                itemId: card.data('item-id'),
                rowId: row.data('row-id'),
                targetQty: qty,
                productName: card.data('product-name')
            };

            $('#modal-product-name').text(currentItemContext.productName);
            $('#modal-target-qty').text(currentItemContext.targetQty);
            
            populateModal();
            $('#serialSelectionModal').modal('show');
        });

        function populateModal() {
            const list = $('#modal-serial-list');
            list.empty();
            const rowId = currentItemContext.rowId;
            const serials = availableSerials[rowId] || [];
            const selected = selectedSerials[rowId] || [];

            const allOtherSelectedIds = [];
            Object.keys(selectedSerials).forEach(rId => {
                if (rId !== rowId) {
                    allOtherSelectedIds.push(...selectedSerials[rId]);
                }
            });

            serials.forEach(s => {
                const isSelectedElsewhere = allOtherSelectedIds.includes(s.id.toString()) || allOtherSelectedIds.includes(s.id);
                if (isSelectedElsewhere) return; 

                const isChecked = selected.includes(s.id.toString()) || selected.includes(s.id);
                list.append(`
                    <div class="col-md-4 mb-2">
                        <div class="form-check border p-2 rounded">
                            <input class="form-check-input ms-0 serial-checkbox" type="checkbox" value="${s.id}" id="s_${s.id}" data-serial-no="${s.serial_no}" ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label ms-2" for="s_${s.id}">${s.serial_no}</label>
                        </div>
                    </div>
                `);
            });
            updateModalCount();
        }

        $(document).on('change', '.serial-checkbox', function() {
            updateModalCount();
        });

        function updateModalCount() {
            const count = $('.serial-checkbox:checked').length;
            $('#modal-current-count').text(count);
            if (count > currentItemContext.targetQty) {
                $('#modal-current-count').addClass('text-danger');
            } else {
                $('#modal-current-count').removeClass('text-danger');
            }
        }

        $('#confirmSerials').on('click', function() {
            const selected = [];
            const serialNos = [];
            $('.serial-checkbox:checked').each(function() {
                selected.push($(this).val());
                serialNos.push($(this).data('serial-no'));
            });

            if (selected.length != currentItemContext.targetQty) {
                toastr.error(`Please select exactly ${currentItemContext.targetQty} serials.`);
                return;
            }

            const rowId = currentItemContext.rowId;
            selectedSerials[rowId] = selected;
            
            const row = $(`.allocation-row[data-row-id="${rowId}"]`);
            const display = row.find('.selected-serials-display');
            const inputs = row.find('.selected-serials-inputs');
            
            display.html('<i class="bx bx-check-double me-1"></i> Selected: ' + serialNos.join(', '));
            
            inputs.empty();
            selected.forEach(id => {
                inputs.append(`<input type="hidden" name="items[${currentItemContext.itemId}][allocations][${rowId}][serials][]" value="${id}">`);
            });

            $('#serialSelectionModal').modal('hide');
        });

        $('#statusUpdateForm').on('submit', function(e) {
            if (statusSelect.val() === 'Shipped') {
                let valid = true;
                $('.allocation-item-card').each(function() {
                    const card = $(this);
                    const target = card.data('target-qty');
                    const warehouseId = card.find('.item-warehouse-select').val();
                    let totalAllocated = 0;
                    
                    if (!warehouseId) {
                        toastr.error(`Please select a warehouse for ${card.data('product-name')}.`);
                        valid = false;
                        return false;
                    }

                    card.find('.allocation-row').each(function() {
                        const row = $(this);
                        const rowId = row.data('row-id');
                        const qty = parseInt(row.find('.allocation-qty').val()) || 0;
                        totalAllocated += qty;

                        if (availableSerials[rowId] && (!selectedSerials[rowId] || selectedSerials[rowId].length !== qty)) {
                            toastr.error(`Please select serials for ${card.data('product-name')} in all batches.`);
                            valid = false;
                            return false;
                        }
                    });

                    if (totalAllocated !== target) {
                        toastr.error(`Total allocated quantity (${totalAllocated}) must equal ordered quantity (${target}) for ${card.data('product-name')}.`);
                        valid = false;
                        return false;
                    }
                });
                
                if (!valid) return false;
            }
        });

        statusSelect.on('change', toggleStatusFields);
        toggleStatusFields();
    });
</script>
@endsection
