@extends('admin.structure.app')

@section('title', 'Warehouse Damage Entry')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 text-danger">Record Warehouse Damage (Wastage)</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.returns.wastages') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('admin.wastage.store') }}" method="POST" id="damage-form">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Damage Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select name="warehouse_id" id="warehouse_id" class="form-select select2" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Batch <span class="text-danger">*</span></label>
                                <select name="batch_id" id="batch_id" class="form-select select2" required disabled>
                                    <option value="">Select Warehouse First</option>
                                </select>
                                @error('batch_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Product / Variant <span class="text-danger">*</span></label>
                                <select name="full_product_id" id="product_id" class="form-select select2" required disabled>
                                    <option value="">Select Batch First</option>
                                </select>
                                <input type="hidden" name="product_id" id="hidden_product_id">
                                <input type="hidden" name="product_variant_id" id="hidden_variant_id">
                                @error('product_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantity" class="form-control" min="1" required disabled>
                                <small class="text-muted" id="available-qty-text"></small>
                                @error('quantity')
                                    <br><span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Serial Numbers</label>
                                <div id="serial-btn-container">
                                    <button type="button" class="btn btn-outline-primary w-100" id="select-serials-btn" disabled>
                                        Select Serials (<span id="serial-count">0</span>)
                                    </button>
                                </div>
                                <div id="selected-serials-inputs"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Damage Reason <span class="text-danger">*</span></label>
                                <input type="text" name="reason" class="form-control" placeholder="e.g., Dropped in warehouse, Water damage" required>
                                @error('reason')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-danger px-5" id="submit-btn">Process Damage Entry</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Serials Modal -->
<div class="modal fade" id="serialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Damaged Serials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small">
                    Only "Good" serials currently in stock are shown.
                </div>
                <div id="modal-serial-list" class="list-group">
                    <!-- Loaded via AJAX -->
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

        const warehouseSelect = $('#warehouse_id');
        const batchSelect = $('#batch_id');
        const productSelect = $('#product_id');
        const qtyInput = $('#quantity');
        const serialBtn = $('#select-serials-btn');
        const serialCountText = $('#serial-count');
        const serialInputs = $('#selected-serials-inputs');
        const availableQtyText = $('#available-qty-text');

        let productHasSerials = false;

        warehouseSelect.on('change', function() {
            const warehouseId = $(this).val();
            batchSelect.empty().append('<option value="">Select Batch</option>');
            productSelect.empty().append('<option value="">Select Product</option>').prop('disabled', true);
            qtyInput.prop('disabled', true).val('');
            serialBtn.prop('disabled', true);
            serialCountText.text('0');
            serialInputs.empty();
            availableQtyText.text('');

            if (warehouseId) {
                $.ajax({
                    url: "{{ route('admin.wastage.ajax.batches') }}",
                    data: { warehouse_id: warehouseId },
                    success: function(batches) {
                        batches.forEach(batch => {
                            batchSelect.append(`<option value="${batch.id}">${batch.batch_number}</option>`);
                        });
                        batchSelect.prop('disabled', false);
                    }
                });
            } else {
                batchSelect.prop('disabled', true);
            }
        });

        batchSelect.on('change', function() {
            const batchId = $(this).val();
            productSelect.empty().append('<option value="">Select Product</option>');
            qtyInput.prop('disabled', true).val('');
            serialBtn.prop('disabled', true);
            serialCountText.text('0');
            serialInputs.empty();
            availableQtyText.text('');

            if (batchId) {
                $.ajax({
                    url: "{{ route('admin.wastage.ajax.products') }}",
                    data: { batch_id: batchId },
                    success: function(products) {
                        products.forEach(bp => {
                            const variantName = bp.variant ? ` (${bp.variant.variant_name})` : '';
                            productSelect.append(`<option value="${bp.product_id}" data-variant-id="${bp.product_variant_id || ''}" data-max="${bp.saleable_qty}">${bp.product.name}${variantName}</option>`);
                        });
                        productSelect.prop('disabled', false);
                    }
                });
            } else {
                productSelect.prop('disabled', true);
            }
        });

        productSelect.on('change', function() {
            const productId = $(this).val();
            const variantId = $(this).find(':selected').data('variant-id');
            const maxQty = $(this).find(':selected').data('max');

            $('#hidden_product_id').val(productId);
            $('#hidden_variant_id').val(variantId);
            
            serialCountText.text('0');
            serialInputs.empty();

            if (productId) {
                qtyInput.prop('disabled', false).attr('max', maxQty);
                availableQtyText.text(`Max available: ${maxQty}`);

                // Check if product has serials
                $.ajax({
                    url: "{{ route('admin.wastage.ajax.serials') }}",
                    data: {
                        batch_id: batchSelect.val(),
                        product_id: productId,
                        product_variant_id: variantId
                    },
                    success: function(serials) {
                        productHasSerials = serials.length > 0;
                        if (productHasSerials) {
                            serialBtn.prop('disabled', false).removeClass('btn-outline-primary').addClass('btn-primary');
                            qtyInput.prop('readonly', true).val('0');
                        } else {
                            serialBtn.prop('disabled', true).removeClass('btn-primary').addClass('btn-outline-primary');
                            qtyInput.prop('readonly', false).val('1');
                        }
                    }
                });
            } else {
                qtyInput.prop('disabled', true).val('');
                availableQtyText.text('');
                serialBtn.prop('disabled', true);
            }
        });

        serialBtn.on('click', function() {
            const batchId = batchSelect.val();
            const productId = productSelect.val();
            const variantId = productSelect.find(':selected').data('variant-id');
            const selectedIds = serialInputs.find('input').map(function() { return $(this).val(); }).get();

            $('#modal-serial-list').html('<div class="text-center p-3"><div class="spinner-border spinner-border-sm" role="status"></div> Loading serials...</div>');
            $('#serialModal').modal('show');

            $.ajax({
                url: "{{ route('admin.wastage.ajax.serials') }}",
                data: {
                    batch_id: batchId,
                    product_id: productId,
                    product_variant_id: variantId
                },
                success: function(serials) {
                    if (serials.length === 0) {
                        $('#modal-serial-list').html('<div class="alert alert-warning mb-0">No available serials found.</div>');
                        return;
                    }

                    let html = '';
                    serials.forEach(serial => {
                        const checked = selectedIds.includes(serial.id.toString()) ? 'checked' : '';
                        html += `
                            <label class="list-group-item d-flex gap-2">
                                <input class="form-check-input flex-shrink-0 serial-checkbox" type="checkbox" value="${serial.id}" ${checked}>
                                <span>${serial.serial_no}</span>
                            </label>
                        `;
                    });
                    $('#modal-serial-list').html(html);
                }
            });
        });

        $('#apply-serials').on('click', function() {
            const selectedCheckboxes = $('.serial-checkbox:checked');
            const selectedCount = selectedCheckboxes.length;

            serialInputs.empty();
            selectedCheckboxes.each(function() {
                serialInputs.append(`<input type="hidden" name="serial_ids[]" value="${$(this).val()}">`);
            });

            serialCountText.text(selectedCount);
            qtyInput.val(selectedCount);

            $('#serialModal').modal('hide');
        });

        qtyInput.on('keyup change', function() {
            const max = parseInt($(this).attr('max'));
            let val = parseInt($(this).val());

            if (productHasSerials) {
                const currentSerials = parseInt(serialCountText.text());
                if (val !== currentSerials) {
                    Toastr.warning(`Quantity is locked to selected serial count (${currentSerials})`);
                    $(this).val(currentSerials);
                }
                return;
            }

            if (val > max) {
                Toastr.warning(`Quantity cannot exceed available stock (${max})`);
                $(this).val(max);
            }
            if (val < 1 || isNaN(val)) {
                $(this).val(1);
            }
        });

        $('#damage-form').on('submit', function(e) {
            if (productHasSerials) {
                const count = parseInt(serialCountText.text());
                if (count === 0) {
                    e.preventDefault();
                    Toastr.error('Please select at least one serial number.');
                    return false;
                }
            }
        });
    });
</script>
@endsection
