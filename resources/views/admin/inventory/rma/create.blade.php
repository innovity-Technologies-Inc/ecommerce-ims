@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Create Supplier RMA</h4>
            <a href="{{ route('admin.inventory.rma.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <form action="{{ route('admin.inventory.rma.store') }}" method="POST" id="rma-form">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Select Damaged Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                    <select name="supplier_id" id="supplier_id" class="form-select select2" required>
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Purchase Order (Optional)</label>
                                    <select name="purchase_order_id" id="purchase_order_id" class="form-select select2">
                                        <option value="">Select PO</option>
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product / Batch</th>
                                            <th style="width: 150px;">Damaged Qty</th>
                                            <th>Serial Numbers</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-body">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                Select a Supplier or PO to load damaged products.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter any additional information..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="notify_supplier" id="notify_supplier" value="1">
                                <label class="form-check-label" for="notify_supplier">Notify Supplier by Email</label>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="submit-btn">Create Return Request</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Serial Selection Modal -->
    <div class="modal fade" id="serialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Serial Numbers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-serial-list" class="list-group">
                        <!-- Serial checkboxes will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="apply-serials">Apply Selection</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        const itemsBody = $('#items-body');
        const supplierSelect = $('#supplier_id');
        const poSelect = $('#purchase_order_id');
        let currentRmaRow = null;

        // Load initial data if values exist (on refresh)
        if (supplierSelect.val()) {
            const initialSupplierId = supplierSelect.val();
            const initialPoId = "{{ old('purchase_order_id') }}"; // Try to get from old input if validation failed

            $.ajax({
                url: "{{ route('admin.inventory.rma.ajax.pos') }}",
                type: 'GET',
                data: { supplier_id: initialSupplierId },
                success: function(pos) {
                    poSelect.empty().append('<option value="">Select PO</option>');
                    if(pos && pos.length > 0) {
                        pos.forEach(po => {
                            const selected = (initialPoId == po.id) ? 'selected' : '';
                            poSelect.append(`<option value="${po.id}" ${selected}>${po.po_number}</option>`);
                        });
                    }
                    poSelect.trigger('change.select2');
                    loadDamagedProducts();
                }
            });
        }

        // Fetch POs based on Supplier
        supplierSelect.on('change', function() {
            const supplierId = $(this).val();
            poSelect.empty().append('<option value="">Select PO</option>');
            
            if (supplierId) {
                $.ajax({
                    url: "{{ route('admin.inventory.rma.ajax.pos') }}",
                    type: 'GET',
                    data: { supplier_id: supplierId },
                    success: function(pos) {
                        if(pos && pos.length > 0) {
                            pos.forEach(po => {
                                poSelect.append(`<option value="${po.id}">${po.po_number}</option>`);
                            });
                        }
                        poSelect.trigger('change');
                    }
                });
            }
            loadDamagedProducts();
        });

        poSelect.on('change', function() {
            loadDamagedProducts();
        });

        function loadDamagedProducts() {
            const supplierId = supplierSelect.val();
            const poId = poSelect.val();

            if (!supplierId && !poId) {
                itemsBody.html('<tr><td colspan="4" class="text-center py-4 text-muted">Select a Supplier or PO to load damaged products.</td></tr>');
                return;
            }

            itemsBody.html('<tr><td colspan="4" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm" role="status"></div> Loading damaged products...</td></tr>');

            $.ajax({
                url: "{{ route('admin.inventory.rma.ajax.batches') }}",
                type: 'GET',
                data: {
                    supplier_id: supplierId,
                    purchase_order_id: poId
                },
                success: function(batches) {
                    if (batches.length === 0) {
                        itemsBody.html('<tr><td colspan="4" class="text-center py-4 text-danger">No damaged products found for selection.</td></tr>');
                        return;
                    }

                    let html = '';
                    batches.forEach((batch, index) => {
                        batch.batch_products.forEach((bp, bpIndex) => {
                            if (bp.damaged_qty > 0) {
                                const rowIndex = `${index}_${bpIndex}`;
                                const variantName = bp.variant ? ` (${bp.variant.variant_name})` : '';
                                
                                html += `
                                    <tr data-row-id="${rowIndex}" 
                                        data-batch-id="${batch.id}" 
                                        data-product-id="${bp.product_id}" 
                                        data-variant-id="${bp.product_variant_id || ''}">
                                        <td>
                                            <div class="fw-medium">${bp.product.name}${variantName}</div>
                                            <div class="small text-muted">
                                                Batch: ${batch.batch_number} | 
                                                PO: ${batch.purchase_order ? batch.purchase_order.po_number : 'Manual'}
                                            </div>
                                            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batch.id}">
                                            <input type="hidden" name="items[${rowIndex}][product_id]" value="${bp.product_id}">
                                            <input type="hidden" name="items[${rowIndex}][product_variant_id]" value="${bp.product_variant_id || ''}">
                                            <div class="selected-serials-container"></div>
                                        </td>
                                        <td>
                                            <input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input" 
                                                value="0" max="${bp.damaged_qty}" min="0" required>
                                            <small class="text-muted">Available Damaged: ${bp.damaged_qty}</small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary select-serials-btn">
                                                Select Serials (<span class="serial-count">0</span>)
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-soft-danger btn-sm remove-row"><i class="bx bx-trash"></i></button>
                                        </td>
                                    </tr>
                                `;
                            }
                        });
                    });

                    itemsBody.html(html || '<tr><td colspan="4" class="text-center py-4 text-danger">No damaged products found for selection.</td></tr>');
                }
            });
        }

        // Open Serial Modal
        $(document).on('click', '.select-serials-btn', function() {
            currentRmaRow = $(this).closest('tr');
            const batchId = currentRmaRow.data('batch-id');
            const productId = currentRmaRow.data('product-id');
            const variantId = currentRmaRow.data('variant-id');
            const container = currentRmaRow.find('.selected-serials-container');
            const selectedIds = container.find('input').map(function() { return $(this).val(); }).get();

            $('#modal-serial-list').html('<div class="text-center p-3"><div class="spinner-border spinner-border-sm" role="status"></div> Loading serials...</div>');
            $('#serialModal').modal('show');

            $.ajax({
                url: "{{ route('admin.inventory.rma.ajax.serials') }}",
                type: 'GET',
                data: {
                    batch_id: batchId,
                    product_id: productId,
                    product_variant_id: variantId
                },
                success: function(serials) {
                    if (serials.length === 0) {
                        $('#modal-serial-list').html('<div class="alert alert-info mb-0">No damaged serials found for this product.</div>');
                        return;
                    }

                    let html = '';
                    serials.forEach(serial => {
                        const checked = selectedIds.includes(serial.id.toString()) ? 'checked' : '';
                        html += `
                            <label class="list-group-item d-flex gap-2">
                                <input class="form-check-input flex-shrink-0 serial-checkbox" type="checkbox" value="${serial.id}" data-serial-no="${serial.serial_no}" ${checked}>
                                <span>${serial.serial_no}</span>
                            </label>
                        `;
                    });
                    $('#modal-serial-list').html(html);
                }
            });
        });

        // Apply Serial Selection
        $('#apply-serials').on('click', function() {
            if (!currentRmaRow) return;

            const rowIndex = currentRmaRow.data('row-id');
            const container = currentRmaRow.find('.selected-serials-container');
            const countSpan = currentRmaRow.find('.serial-count');
            const qtyInput = currentRmaRow.find('.qty-input');
            const maxQty = parseInt(qtyInput.attr('max'));

            const selectedCheckboxes = $('.serial-checkbox:checked');
            const selectedCount = selectedCheckboxes.length;

            container.empty();
            selectedCheckboxes.each(function() {
                container.append(`<input type="hidden" name="items[${rowIndex}][serial_ids][]" value="${$(this).val()}">`);
            });

            countSpan.text(selectedCount);

            if (selectedCount > 0) {
                qtyInput.val(selectedCount).attr('readonly', true);
            } else {
                qtyInput.val(0).attr('readonly', false);
            }

            $('#serialModal').modal('hide');
        });

        $(document).on('change keyup', '.qty-input', function() {
            const row = $(this).closest('tr');
            const selectedCount = parseInt(row.find('.serial-count').text());
            const max = parseInt($(this).attr('max'));
            const val = parseInt($(this).val());

            if (selectedCount > 0 && val !== selectedCount) {
                Toastr.warning(`Quantity must match selected serial count (${selectedCount})`);
                $(this).val(selectedCount);
            }

            if (val > max) {
                Toastr.warning(`Quantity cannot exceed available damaged quantity (${max})`);
                $(this).val(max);
            }

            if (val < 0 || isNaN(val)) {
                $(this).val(0);
            }
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            if (itemsBody.children().length === 0) {
                itemsBody.html('<tr><td colspan="4" class="text-center py-4 text-muted">Select a Supplier or PO to load damaged products.</td></tr>');
            }
        });

        $('#rma-form').on('submit', function(e) {
            let hasItems = false;
            $('.qty-input').each(function() {
                if (parseInt($(this).val()) > 0) {
                    hasItems = true;
                    return false;
                }
            });

            if (!hasItems) {
                e.preventDefault();
                Toastr.error('Please enter a quantity greater than 0 for at least one item.');
                return false;
            }

            let valid = true;
            // Additional client side validation if needed
            if (!valid) e.preventDefault();
        });
    });
</script>
@endsection
