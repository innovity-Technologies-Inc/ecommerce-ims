@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Return Request #{{ $request->return_id }}</h4>
        <a href="{{ route('admin.returns.requests') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i>Back to List
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Return Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th class="text-center">Return Qty</th>
                                    <th class="text-center">Condition</th>
                                    <th class="text-end pe-3">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($request->returnItems as $item)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $item->product->primaryImage ? asset('storage/'.$item->product->primaryImage->image_path) : asset('admin_assets/images/no-image.png') }}" class="rounded-pill" style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fs-14">{{ $item->product->name }}</h6>
                                                    @if($item->productVariant)
                                                        <small class="text-muted">{{ $item->productVariant->variant_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-center">
                                            @if($request->status === 'pending')
                                                <span class="badge bg-secondary-subtle text-secondary">Pending</span>
                                            @else
                                                <span class="badge {{ $item->condition === 'intact' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                    {{ ucfirst($item->condition) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Return Reason & Image</h5>
                </div>
                <div class="card-body">
                    <p class="mb-4"><strong>Reason:</strong><br>{{ $request->reason }}</p>
                    @if($request->image)
                        <h6 class="mb-3">Uploaded Image:</h6>
                        <img src="{{ asset('storage/' . $request->image) }}" class="img-fluid rounded border shadow-sm" style="max-width: 300px;">
                    @else
                        <div class="alert alert-secondary py-2">No image uploaded.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer & Order Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><strong>Order ID:</strong> #{{ $request->order->order_id }}</li>
                        <li class="mb-2"><strong>Customer:</strong> {{ $request->user ? $request->user->name : 'Guest' }}</li>
                        <li class="mb-2"><strong>Email:</strong> {{ $request->user ? $request->user->email : $request->order->email }}</li>
                        <li class="mb-2"><strong>Mobile:</strong> {{ $request->user ? $request->user->mobile : $request->order->mobile }}</li>
                        <li class="mb-0"><strong>Status:</strong> 
                            <span class="badge {{ match($request->status) {
                                'pending' => 'bg-warning-subtle text-warning',
                                'approved' => 'bg-info-subtle text-info',
                                'received' => 'bg-success-subtle text-success',
                                'rejected' => 'bg-danger-subtle text-danger',
                                default => 'bg-secondary-subtle text-secondary'
                            } }} px-2 py-1">
                                {{ ucfirst($request->status) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            @if($request->status === 'pending')
                @can('returns.edit')
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Action</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.returns.update_status', $request->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Update Status</label>
                                <select name="status" id="status_toggle" class="form-select" required>
                                    <option value="">Select Action</option>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                </select>
                            </div>

                            <div id="rejection_container" class="d-none mb-3">
                                <label class="form-label">Rejection Reason</label>
                                <textarea name="rejection_reason" class="form-control" rows="3"></textarea>
                            </div>

                            <div id="condition_container" class="d-none mt-3">
                                <h6 class="text-uppercase fw-bold text-primary mb-3">Return Allocation</h6>
                                @foreach($request->returnItems as $item)
                                    <div class="card border mb-3 shadow-none return-item-card" 
                                         data-item-id="{{ $item->id }}" 
                                         data-order-item-id="{{ \App\Models\OrderItem::where('order_id', $request->order_id)->where('product_id', $item->product_id)->where('product_variant_id', $item->product_variant_id)->first()->id ?? 0 }}"
                                         data-target-qty="{{ $item->quantity }}"
                                         data-product-name="{{ $item->product->name }}">
                                        <div class="card-header bg-light-subtle py-2 d-flex justify-content-between align-items-center">
                                            <div class="fw-bold small text-dark">
                                                {{ $item->product->name }}
                                                <span class="badge bg-soft-primary text-primary ms-2">Return Qty: {{ $item->quantity }}</span>
                                            </div>
                                            <div class="allocation-status small">
                                                Allocated: <span class="current-allocated-qty fw-bold">0</span> / {{ $item->quantity }}
                                            </div>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold text-muted mb-1">Condition <span class="text-danger">*</span></label>
                                                <select name="items[{{ $item->id }}][condition]" class="form-select form-select-sm" required>
                                                    <option value="intact">Intact (Restock)</option>
                                                    <option value="damage">Damage (Wastage)</option>
                                                </select>
                                            </div>

                                            <div class="allocation-rows-container mt-2">
                                                {{-- Allocation rows will be added here --}}
                                            </div>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-soft-success add-allocation-row-btn">
                                                    <i class="bx bx-plus me-1"></i> Add Batch Allocation
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Confirm Action</button>
                        </form>
                    </div>
                </div>
                @else
                <div class="alert alert-info border-0 mb-0" role="alert">
                    <i class="bx bx-info-circle me-1"></i> You do not have permission to approve/reject returns.
                </div>
                @endcan
            @elseif($request->status === 'approved')
                @can('returns.edit')
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">Receiving Workflow</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-4">The return request is approved. Once you have physically received the products, click the button below to process stock and sales adjustments.</p>
                        <form action="{{ route('admin.returns.receive', $request->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                                <i class="bx bx-check-circle me-1"></i> MARK AS RECEIVED
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="alert alert-info border-0 mb-0" role="alert">
                    <i class="bx bx-info-circle me-1"></i> You do not have permission to receive returns.
                </div>
                @endcan
            @elseif($request->status === 'rejected')
                <div class="card border-danger mb-3">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0 text-white">Rejection Details</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><strong>Reason:</strong><br>{{ $request->rejection_reason }}</p>
                    </div>
                </div>
            @endif
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
        let currentItemContext = null;
        let availableSerials = {};
        let selectedSerials = {};

        $('#status_toggle').on('change', function() {
            const val = $(this).val();
            if (val === 'approved') {
                $('#condition_container').removeClass('d-none');
                $('#rejection_container').addClass('d-none');
                $('#rejection_container textarea').removeAttr('required');
                initializeReturnAllocation();
            } else if (val === 'rejected') {
                $('#rejection_container').removeClass('d-none');
                $('#rejection_container textarea').attr('required', 'required');
                $('#condition_container').addClass('d-none');
            } else {
                $('#condition_container').addClass('d-none');
                $('#rejection_container').addClass('d-none');
            }
        });

        function initializeReturnAllocation() {
            $('.return-item-card').each(function() {
                const card = $(this);
                if (card.find('.allocation-row').length === 0) {
                    addReturnAllocationRow(card);
                }
            });
        }

        function addReturnAllocationRow(card) {
            const itemId = card.data('item-id');
            const orderItemId = card.data('order-item-id');
            const rowUniqueId = `row_${itemId}_${Date.now()}_${Math.floor(Math.random() * 1000)}`;

            const rowHtml = `
                <div class="allocation-row border rounded p-2 mb-2 bg-white" data-row-id="${rowUniqueId}">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <label class="small text-muted mb-1 d-block">Batch <span class="text-danger">*</span></label>
                            <select name="items[${itemId}][allocations][${rowUniqueId}][batch_id]" class="form-select form-select-sm return-batch-select" data-order-item-id="${orderItemId}" required>
                                <option value="">Select Batch</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted mb-1 d-block">Qty <span class="text-danger">*</span></label>
                            <input type="number" name="items[${itemId}][allocations][${rowUniqueId}][quantity]" class="form-control form-control-sm allocation-qty" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-md-2 text-end pt-3">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn d-none">
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
            
            if (card.find('.allocation-row').length > 1) {
                $row.find('.remove-row-btn').removeClass('d-none');
            }

            // Load batches from ORIGINAL ORDER
            const batchSelect = $row.find('.return-batch-select');
            $.ajax({
                url: "{{ route('admin.returns.ajax.get-order-batches') }}",
                data: { order_item_id: orderItemId },
                success: function(data) {
                    batchSelect.empty().append('<option value="">Select Batch</option>');
                    data.forEach(b => {
                        batchSelect.append(`<option value="${b.id}">${b.batch_number} (Shipped: ${b.shipped_qty})</option>`);
                    });
                }
            });
        }

        $(document).on('click', '.add-allocation-row-btn', function() {
            addReturnAllocationRow($(this).closest('.return-item-card'));
        });

        $(document).on('click', '.remove-row-btn', function() {
            const row = $(this).closest('.allocation-row');
            const card = row.closest('.return-item-card');
            const rowId = row.data('row-id');
            delete availableSerials[rowId];
            delete selectedSerials[rowId];
            row.remove();
            calculateReturnAllocatedTotal(card);
        });

        $(document).on('change', '.return-batch-select', function() {
            const select = $(this);
            const batchId = select.val();
            const row = select.closest('.allocation-row');
            const orderItemId = select.data('order-item-id');
            const serialContainer = row.find('.serial-container');
            const rowId = row.data('row-id');

            row.find('.selected-serials-display').empty();
            row.find('.selected-serials-inputs').empty();
            delete selectedSerials[rowId];

            if (batchId) {
                $.ajax({
                    url: "{{ route('admin.returns.ajax.get-order-serials') }}",
                    data: { order_item_id: orderItemId, batch_id: batchId },
                    success: function(data) {
                        if (data && data.length > 0) {
                            availableSerials[rowId] = data;
                            selectedSerials[rowId] = [];
                            serialContainer.removeClass('d-none');
                        } else {
                            availableSerials[rowId] = null;
                            serialContainer.addClass('d-none');
                        }
                    }
                });
            } else {
                serialContainer.addClass('d-none');
            }
        });

        $(document).on('input', '.allocation-qty', function() {
            calculateReturnAllocatedTotal($(this).closest('.return-item-card'));
        });

        function calculateReturnAllocatedTotal(card) {
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
            const card = row.closest('.return-item-card');
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
            
            populateSerialModal();
            $('#serialSelectionModal').modal('show');
        });

        function populateSerialModal() {
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
            updateSerialModalCount();
        }

        $(document).on('change', '.serial-checkbox', function() {
            updateSerialModalCount();
        });

        function updateSerialModalCount() {
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
                inputs.append(`<input type="hidden" name="items[${currentItemContext.itemId}][allocations][${rowId}][batch_serial_id]" value="${id}">`);
            });

            $('#serialSelectionModal').modal('hide');
        });

        $('form').on('submit', function(e) {
            if ($('#status_toggle').val() === 'approved') {
                let valid = true;
                $('.return-item-card').each(function() {
                    const card = $(this);
                    const target = card.data('target-qty');
                    let totalAllocated = 0;
                    
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
                        toastr.error(`Total allocated quantity (${totalAllocated}) must equal return quantity (${target}) for ${card.data('product-name')}.`);
                        valid = false;
                        return false;
                    }
                });
                
                if (!valid) return false;
            }
        });
    });
</script>
@endsection
