@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-8">
                {{-- Order Items Table --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Order Details: {{ $order->order_id }}</h5>
                        <span class="text-muted">{{ $order->created_at->format('d M, Y h:i A') }}</span>
                    </div>
                    <div class="card-body">
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
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-2">
                                                    <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                    @if($item->variant_name)
                                                        <small class="text-muted">{{ $item->variant_name }}</small>
                                                    @endif

                                                    {{-- Fulfillment Details --}}
                                                    @if($item->warehouse_id && $item->batch_id)
                                                        <div class="mt-2 pt-2 border-top border-dashed">
                                                            <div class="d-flex flex-wrap gap-2">
                                                                <span class="badge badge-soft-info" title="Warehouse">
                                                                    <i class="bx bx-home-alt me-1"></i> {{ $item->warehouse->name }}
                                                                </span>
                                                                <span class="badge badge-soft-secondary" title="Batch">
                                                                    <i class="bx bx-purchase-tag-alt me-1"></i> {{ $item->batch->batch_number }}
                                                                </span>
                                                            </div>
                                                            @if($item->serials->count() > 0)
                                                                <div class="mt-1 small">
                                                                    <strong class="text-muted">Serials:</strong>
                                                                    <span class="text-primary">{{ $item->serials->pluck('serial_no')->implode(', ') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->total_price, 2) }}</td>
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
                                            <td class="text-end">${{ number_format($order->discount, 2) }}</td>
                                        </tr>
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
                                    <div class="row">
                                        @foreach($order->orderItems as $item)
                                            <div class="col-md-6">
                                                <div class="card border mb-3 shadow-none">
                                                    <div class="card-body p-2">
                                                        <div class="fw-bold small mb-2 text-dark">{{ $item->product_name }} (Qty: {{ $item->quantity }})</div>
                                                        
                                                        <div class="mb-2">
                                                            <select name="items[{{ $item->id }}][warehouse_id]" class="form-select form-select-sm warehouse-select" data-product-id="{{ $item->product_id }}" data-variant-id="{{ $item->product_variant_id }}" required>
                                                                <option value="">Select Warehouse</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-2 batch-container d-none">
                                                            <select name="items[{{ $item->id }}][batch_id]" class="form-select form-select-sm batch-select" required>
                                                                <option value="">Select Batch</option>
                                                            </select>
                                                        </div>

                                                        <div class="serial-container d-none">
                                                            <label class="small text-muted d-block mb-1">Serials ({{ $item->quantity }})</label>
                                                            <button type="button" class="btn btn-sm btn-soft-primary select-serials-btn" 
                                                                    data-item-id="{{ $item->id }}" 
                                                                    data-qty="{{ $item->quantity }}"
                                                                    data-product-name="{{ $item->product_name }}">
                                                                <i class="bx bx-list-check me-1"></i> Select Serials
                                                            </button>
                                                            <div class="selected-serials-display mt-2 small text-primary fw-bold"></div>
                                                            <div class="selected-serials-inputs"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
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
        
        let currentItemContext = null;
        let availableSerials = {}; // Store serials for each item: { itemId: [ {id, serial_no} ] }
        let selectedSerials = {}; // Store selected IDs for each item: { itemId: [id1, id2] }

        function toggleStatusFields() {
            const status = statusSelect.val();
            
            // Rejection/Cancellation Logic
            if (status === 'Cancelled' || status === 'Rejected') {
                reasonWrapper.removeClass('d-none');
                reasonInput.attr('required', 'required');
            } else {
                reasonWrapper.addClass('d-none');
                reasonInput.removeAttr('required');
            }

            // Inventory Allocation Logic
            if (status === 'Shipped') {
                allocationSection.removeClass('d-none');
                allocationSection.find('select.warehouse-select').attr('required', 'required');
                initializeAllocation();
            } else {
                allocationSection.addClass('d-none');
                allocationSection.find('select').removeAttr('required');
            }
        }

        function initializeAllocation() {
            $('.warehouse-select').each(function() {
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

        $(document).on('change', '.warehouse-select', function() {
            const select = $(this);
            const warehouseId = select.val();
            const cardBody = select.closest('.card-body');
            const batchContainer = cardBody.find('.batch-container');
            const batchSelect = cardBody.find('.batch-select');
            const productId = select.data('product-id');
            const variantId = select.data('variant-id');

            if (warehouseId) {
                batchSelect.prop('disabled', true).empty().append('<option value="">Loading batches...</option>');
                batchContainer.removeClass('d-none');
                
                $.ajax({
                    url: "{{ route('admin.orders.ajax.get-batches') }}",
                    data: { warehouse_id: warehouseId, product_id: productId, variant_id: variantId },
                    success: function(data) {
                        batchSelect.prop('disabled', false).empty().append('<option value="">Select Batch</option>');
                        data.forEach(b => {
                            batchSelect.append(`<option value="${b.id}">${b.batch_number}</option>`);
                        });
                        
                        if (!batchSelect.hasClass('select2-hidden-accessible')) {
                            batchSelect.select2({ theme: 'bootstrap-5', dropdownParent: cardBody });
                        }
                    }
                });
            } else {
                batchContainer.addClass('d-none');
                cardBody.find('.serial-container').addClass('d-none');
            }
        });

        $(document).on('change', '.batch-select', function() {
            const select = $(this);
            const batchId = select.val();
            const cardBody = select.closest('.card-body');
            const serialContainer = cardBody.find('.serial-container');
            const btn = cardBody.find('.select-serials-btn');
            const itemId = btn.data('item-id');
            const warehouseSelect = cardBody.find('.warehouse-select');
            const productId = warehouseSelect.data('product-id');
            const variantId = warehouseSelect.data('variant-id');

            if (batchId) {
                $.ajax({
                    url: "{{ route('admin.orders.ajax.get-serials') }}",
                    data: { batch_id: batchId, product_id: productId, variant_id: variantId },
                    success: function(data) {
                        if (data && data.length > 0) {
                            availableSerials[itemId] = data;
                            selectedSerials[itemId] = []; // Reset on batch change
                            serialContainer.removeClass('d-none');
                            updateSelectedSerialsDisplay(itemId);
                        } else {
                            availableSerials[itemId] = null;
                            serialContainer.addClass('d-none');
                        }
                    }
                });
            } else {
                serialContainer.addClass('d-none');
            }
        });

        $(document).on('click', '.select-serials-btn', function() {
            const btn = $(this);
            currentItemContext = {
                itemId: btn.data('item-id'),
                targetQty: btn.data('qty'),
                productName: btn.data('product-name')
            };

            $('#modal-product-name').text(currentItemContext.productName);
            $('#modal-target-qty').text(currentItemContext.targetQty);
            
            populateModal();
            $('#serialSelectionModal').modal('show');
        });

        function populateModal() {
            const list = $('#modal-serial-list');
            list.empty();
            const itemId = currentItemContext.itemId;
            const serials = availableSerials[itemId] || [];
            const selected = selectedSerials[itemId] || [];

            serials.forEach(s => {
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

            const itemId = currentItemContext.itemId;
            selectedSerials[itemId] = selected;
            updateSelectedSerialsDisplay(itemId, serialNos);
            $('#serialSelectionModal').modal('hide');
        });

        function updateSelectedSerialsDisplay(itemId, serialNos = []) {
            const container = $(`.select-serials-btn[data-item-id="${itemId}"]`).closest('.serial-container');
            const display = container.find('.selected-serials-display');
            const inputs = container.find('.selected-serials-inputs');
            
            if (serialNos.length > 0) {
                display.html('<i class="bx bx-check-double me-1"></i> Selected: ' + serialNos.join(', '));
            } else {
                display.html('<span class="text-danger">No serials selected</span>');
            }

            inputs.empty();
            const ids = selectedSerials[itemId] || [];
            ids.forEach(id => {
                inputs.append(`<input type="hidden" name="items[${itemId}][serials][]" value="${id}">`);
            });
        }

        $('#statusUpdateForm').on('submit', function(e) {
            if (statusSelect.val() === 'Shipped') {
                let valid = true;
                $('.select-serials-btn:visible').each(function() {
                    const itemId = $(this).data('item-id');
                    const qty = $(this).data('qty');
                    const selected = selectedSerials[itemId] ? selectedSerials[itemId].length : 0;
                    if (selected != qty) {
                        toastr.error(`Please select serials for ${$(this).data('product-name')}`);
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
