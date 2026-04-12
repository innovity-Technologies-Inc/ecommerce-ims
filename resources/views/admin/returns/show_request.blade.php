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
        <div class="col-lg-9">
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

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Return Reason & Proof Images</h5>
                </div>
                <div class="card-body">
                    <p class="mb-4"><strong>Reason:</strong><br>{{ $request->reason }}</p>
                    
                    <h6 class="mb-3">Proof Images:</h6>
                    <div class="row g-2">
                        @forelse($request->returnImages as $image)
                            <div class="col-md-3 col-sm-4 col-6">
                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid rounded border shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                                </a>
                            </div>
                        @empty
                            @if($request->image)
                                <div class="col-md-3 col-sm-4 col-6">
                                    <a href="{{ asset('storage/' . $request->image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $request->image) }}" class="img-fluid rounded border shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                                    </a>
                                    <small class="text-muted d-block mt-1">Primary Image</small>
                                </div>
                            @else
                                <div class="col-12">
                                    <div class="alert alert-secondary py-2 mb-0">No images uploaded.</div>
                                </div>
                            @endif
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
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
        </div>

        <div class="col-12">
            @if($request->status === 'pending')
                @can('returns.edit')
                <div class="card mb-3">
                    <div class="card-header bg-light-subtle">
                        <h5 class="card-title mb-0">Update Return Request Status</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.returns.update_status', $request->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold fs-15 mb-md-0">Decision Action <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="status" id="status_toggle" class="form-select form-select-lg shadow-sm" required>
                                                    <option value="">Choose an action...</option>
                                                    <option value="approved">Approve Return Request</option>
                                                    <option value="rejected">Reject Return Request</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="rejection_container" class="d-none mt-3">
                                        <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                                        <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Briefly explain why the return is rejected..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-3 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Confirm & Update Request</button>
                            </div>
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
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="card-title mb-0 text-white"><i class="bx bx-package me-2"></i>Physical Receiving & Stock Allocation</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">The return request is approved. Inspect the returned items and allocate them to the correct batches/serials below to complete the restoration or wastage process.</p>
                        
                        <form action="{{ route('admin.returns.receive', $request->id) }}" method="POST" id="receive_return_form">
                            @csrf
                            <div id="condition_container">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-0">Allocation & Condition Management</h6>
                                    <span class="badge bg-soft-info text-info">
                                        <i class="bx bx-info-circle me-1"></i> Selection is restricted to items originally sold in this order
                                    </span>
                                </div>

                                @foreach($request->returnItems as $item)
                                    @php
                                        $orderItem = \App\Models\OrderItem::where('order_id', $request->order_id)
                                            ->where('product_id', $item->product_id)
                                            ->where('product_variant_id', $item->product_variant_id)
                                            ->first();
                                    @endphp
                                    <div class="card border mb-4 shadow-none return-item-card" 
                                         data-item-id="{{ $item->id }}" 
                                         data-order-item-id="{{ $orderItem->id ?? 0 }}"
                                         data-target-qty="{{ $item->quantity }}"
                                         data-product-name="{{ $item->product->name }}">
                                        <div class="card-header bg-light-subtle py-2 d-flex justify-content-between align-items-center border-bottom">
                                            <div class="fw-bold text-dark">
                                                <i class="bx bx-package me-1 text-muted"></i> {{ $item->product->name }}
                                                <span class="badge bg-primary ms-2">Requested: {{ $item->quantity }}</span>
                                            </div>
                                            <div class="allocation-status">
                                                <span class="text-muted small">Total Allocated:</span> 
                                                <span class="current-allocated-qty fw-bold fs-15">0</span> 
                                                <span class="text-muted">/ {{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-4">
                                                <div class="col-lg-3 border-end">
                                                    <div class="mb-4">
                                                        <label class="form-label small fw-bold text-uppercase text-muted mb-2">Item Condition <span class="text-danger">*</span></label>
                                                        <select name="items[{{ $item->id }}][condition]" class="form-select shadow-sm" required>
                                                            <option value="intact">Intact (Restock)</option>
                                                            <option value="damage">Damage (Wastage)</option>
                                                        </select>
                                                        <div class="form-text small">Select 'Intact' to return items to saleable stock.</div>
                                                    </div>
                                                    
                                                    <button type="button" class="btn btn-sm btn-soft-success add-allocation-row-btn w-100 py-2">
                                                        <i class="bx bx-plus-circle me-1"></i> Add Batch Split
                                                    </button>
                                                </div>
                                                <div class="col-lg-9">
                                                    <label class="form-label small fw-bold text-uppercase text-muted mb-2">Batch & Unit Allocation</label>
                                                    <div class="allocation-rows-container">
                                                        {{-- Allocation rows will be added here --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-top pt-3 text-end mt-4">
                                <button type="submit" class="btn btn-success px-5 py-2 fw-bold">
                                    <i class="bx bx-check-circle me-1"></i> CONFIRM & PROCESS RECEIPT
                                </button>
                            </div>
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
            if (val === 'rejected') {
                $('#rejection_container').removeClass('d-none');
                $('#rejection_container textarea').attr('required', 'required');
            } else {
                $('#rejection_container').addClass('d-none');
                $('#rejection_container textarea').removeAttr('required');
            }
        });

        function initializeReturnAllocation() {
            if ($('#condition_container').length) {
                $('.return-item-card').each(function() {
                    const card = $(this);
                    if (card.find('.allocation-row').length === 0) {
                        addReturnAllocationRow(card);
                    }
                });
            }
        }
        
        // Initialize if container exists (Approved state)
        initializeReturnAllocation();

        function addReturnAllocationRow(card) {
            const itemId = card.data('item-id');
            const orderItemId = card.data('order-item-id');
            const rowUniqueId = `row_${itemId}_${Date.now()}_${Math.floor(Math.random() * 1000)}`;

            const rowHtml = `
                <div class="allocation-row border rounded p-3 mb-3 bg-white shadow-sm" data-row-id="${rowUniqueId}">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-7">
                            <label class="small fw-bold text-muted mb-1 d-block text-uppercase">Batch Number <span class="text-danger">*</span></label>
                            <select name="items[${itemId}][allocations][${rowUniqueId}][batch_id]" class="form-select return-batch-select" data-order-item-id="${orderItemId}" required>
                                <option value="">Select Batch</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted mb-1 d-block text-uppercase">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="items[${itemId}][allocations][${rowUniqueId}][quantity]" class="form-control allocation-qty" placeholder="0" min="1" required>
                        </div>
                        <div class="col-md-2 text-end pt-3">
                            <button type="button" class="btn btn-outline-danger remove-row-btn d-none">
                                <i class="bx bx-trash me-1"></i>Remove
                            </button>
                        </div>
                    </div>
                    <div class="serial-container d-none mt-3 p-3 bg-light rounded-3 border-dashed border-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="small fw-bold text-primary mb-0 text-uppercase">Serial Verification Required</label>
                            <button type="button" class="btn btn-sm btn-primary select-serials-btn" 
                                    data-item-id="${itemId}" 
                                    data-row-id="${rowUniqueId}">
                                <i class="bx bx-list-check me-1"></i> Select physical units
                            </button>
                        </div>
                        <div class="selected-serials-display mt-2 p-2 bg-white rounded border small text-primary fw-bold">
                            <i class="bx bx-info-circle me-1"></i> No serials selected yet
                        </div>
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
                inputs.append(`<input type="hidden" name="items[${currentItemContext.itemId}][allocations][${rowId}][batch_serial_ids][]" value="${id}">`);
            });

            $('#serialSelectionModal').modal('hide');
        });

        $('#receive_return_form').on('submit', function(e) {
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
        });
    });
</script>
@endsection
