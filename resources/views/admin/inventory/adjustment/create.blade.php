@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">New Stock Adjustment</h4>
            <a href="{{ route('admin.inventory.adjustment.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.inventory.adjustment.store') }}" method="POST" id="adjustment-form">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">General Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-select select2" required>
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id')
                                        <span class="small text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Batch Number <span class="text-danger">*</span></label>
                                    <input type="text" name="batch_number" class="form-control" placeholder="Enter Batch #" value="{{ old('batch_number', $generatedBatchNumber) }}" required>
                                    <small class="text-muted">Auto-generated. You can edit this if needed.</small>
                                    @error('batch_number')
                                        <br><span class="small text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Adjustment Date <span class="text-danger">*</span></label>
                                    <input type="date" name="adjustment_date" class="form-control" value="{{ old('adjustment_date', date('Y-m-d')) }}" required>
                                    @error('adjustment_date')
                                        <span class="small text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Adjustment Items</h5>
                            <button type="button" class="btn btn-primary btn-sm" id="add-item-btn">
                                <i class="bx bx-plus"></i> Add Item
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product / Variant</th>
                                            <th style="width: 120px;">Qty</th>
                                            <th style="width: 150px;">Unit Cost</th>
                                            <th>Serial Numbers (Optional)</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-body">
                                        <!-- Row Template -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Explain the reason for this adjustment..."></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-3 mb-4">
                        <button type="submit" class="btn btn-primary px-5">Process Adjustment</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Hidden Template Row -->
    <table style="display: none;">
        <tbody id="row-template">
            <tr class="item-row">
                <td>
                    <select class="form-select product-select" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            @if($product->variants->count() > 0)
                                @foreach($product->variants as $variant)
                                    <option value="{{ $product->id }}" 
                                            data-variant-id="{{ $variant->id }}"
                                            data-unit-cost="{{ $variant->unit_cost ?? $product->unit_cost ?? 0 }}">
                                        {{ $product->name }} ({{ $variant->variant_name }})
                                    </option>
                                @endforeach
                            @else
                                <option value="{{ $product->id }}" 
                                        data-variant-id=""
                                        data-unit-cost="{{ $product->unit_cost ?? 0 }}">
                                    {{ $product->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <input type="hidden" class="hidden-product-id">
                    <input type="hidden" class="hidden-variant-id">
                </td>
                <td>
                    <input type="number" class="form-control qty-input" min="1" value="1" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control cost-input" placeholder="0.00" required>
                </td>
                <td>
                    <div class="serial-container">
                        <select class="form-control serial-tags" multiple="multiple">
                        </select>
                    </div>
                    <div class="small text-muted mt-1 serial-count"></div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-soft-danger btn-sm remove-row"><i class="bx bx-trash"></i></button>
                </td>
            </tr>
        </tbody>
    </table>

@endsection

@section('scripts')
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 38px;
        max-height: 100px; /* Limit height and scroll vertically */
        overflow-y: auto; 
        overflow-x: hidden;
        border: 1px solid #dee2e6;
        border-radius: 0;
        display: flex;
        align-items: center;
        width: 100%;
        scrollbar-width: thin;
    }
    /* Webkit scrollbar styling */
    .select2-container--bootstrap-5 .select2-selection--multiple::-webkit-scrollbar {
        width: 4px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 10px;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection--multiple {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        padding: 0 0.5rem;
        display: flex;
        flex-wrap: wrap; /* Re-enable wrapping for vertical growth */
        align-items: center;
        width: 100%;
        margin: 0;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-search {
        flex-grow: 1;
        display: flex;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-search__field {
        margin: 0;
        padding-left: 0.25rem;
        height: 34px;
        font-family: inherit;
        line-height: 34px;
        width: 100% !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        margin-top: 2px;
        margin-bottom: 2px;
        white-space: nowrap;
    }
    .serial-container {
        min-width: 250px;
    }
</style>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        const itemsBody = $('#items-body');
        let rowIndex = 0;

        function addRow() {
            const template = $('#row-template').html();
            const newRow = $(template);
            
            // Update names for backend array handling
            newRow.find('.product-select').attr('name', `items[${rowIndex}][product_full_id]`).select2({ theme: 'bootstrap-5' });
            newRow.find('.hidden-product-id').attr('name', `items[${rowIndex}][product_id]`);
            newRow.find('.hidden-variant-id').attr('name', `items[${rowIndex}][product_variant_id]`);
            newRow.find('.qty-input').attr('name', `items[${rowIndex}][quantity]`);
            newRow.find('.cost-input').attr('name', `items[${rowIndex}][unit_cost]`);
            
            const serialSelect = newRow.find('.serial-tags');
            serialSelect.attr('name', `items[${rowIndex}][serial_numbers][]`).select2({
                theme: 'bootstrap-5',
                tags: true,
                tokenSeparators: [',', ' '],
                placeholder: 'Add serial numbers'
            });

            // Prevent exceeding quantity during selection
            serialSelect.on('select2:selecting', function(e) {
                let row = $(this).closest('tr');
                let qty = parseInt(row.find('.qty-input').val()) || 0;
                let currentCount = $(this).val() ? $(this).val().length : 0;

                if (currentCount >= qty) {
                    Toastr.warning(`Cannot exceed the quantity (${qty}).`);
                    e.preventDefault();
                }
            });

            serialSelect.on('change', function() {
                let count = $(this).val() ? $(this).val().length : 0;
                $(this).closest('td').find('.serial-count').text(count > 0 ? `Selected: ${count} serials` : '');
            });

            itemsBody.append(newRow);
            rowIndex++;
        }

        $('#add-item-btn').on('click', addRow);

        // Initial row
        addRow();

        $(document).on('change', '.product-select', function() {
            const row = $(this).closest('tr');
            const selected = $(this).find(':selected');
            row.find('.hidden-product-id').val(selected.val());
            row.find('.hidden-variant-id').val(selected.data('variant-id'));
            
            // Auto-fill unit cost
            const unitCost = selected.data('unit-cost');
            if (unitCost !== undefined) {
                row.find('.cost-input').val(unitCost);
            }
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            if (itemsBody.children().length === 0) {
                addRow();
            }
        });

        $('#adjustment-form').on('submit', function(e) {
            let isValid = true;
            $('#items-body tr').each(function() {
                const row = $(this);
                const productText = row.find('.product-select option:selected').text();
                const qty = parseInt(row.find('.qty-input').val()) || 0;
                const serials = row.find('.serial-tags').val() || [];

                if (serials.length > 0 && serials.length !== qty) {
                    Toastr.error(`Serial count (${serials.length}) for ${productText} must match Qty (${qty}).`);
                    isValid = false;
                    return false;
                }
            });

            if (!isValid) e.preventDefault();
        });
    });
</script>
@endsection
