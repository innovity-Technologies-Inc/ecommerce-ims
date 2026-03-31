@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">New Stock Adjustment</h4>
            <a href="{{ route('admin.inventory.adjustment.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <form action="{{ route('admin.inventory.adjustment.store') }}" method="POST" id="adjustment-form">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">General Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-select select2" required>
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Batch Number <span class="text-danger">*</span></label>
                                    <input type="text" name="batch_number" class="form-control" placeholder="Enter Batch #" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Adjustment Date <span class="text-danger">*</span></label>
                                    <input type="date" name="adjustment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
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
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Process Adjustment</button>
                            </div>
                        </div>
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
                                    <option value="{{ $product->id }}" data-variant-id="{{ $variant->id }}">{{ $product->name }} ({{ $variant->variant_name }})</option>
                                @endforeach
                            @else
                                <option value="{{ $product->id }}" data-variant-id="">{{ $product->name }}</option>
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
                    <textarea class="form-control serial-input" rows="1" placeholder="Comma separated serials"></textarea>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-soft-danger btn-sm remove-row"><i class="bx bx-trash"></i></button>
                </td>
            </tr>
        </tbody>
    </table>

@endsection

@section('scripts')
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
            newRow.find('.serial-input').attr('name', `items[${rowIndex}][serial_numbers]`);

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
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            if (itemsBody.children().length === 0) {
                addRow();
            }
        });
    });
</script>
@endsection
