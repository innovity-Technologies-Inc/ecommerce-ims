@extends('admin.structure.app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create Purchase Order</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.inventory.po.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.inventory.po.store') }}" method="POST" id="poForm">
        @csrf
        <div class="row">
            <!-- Order Information - Full Width Row -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Order Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" id="supplier_id" class="form-select select2 @error('supplier_id') is-invalid @enderror" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="warehouse_id" class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select name="warehouse_id" id="warehouse_id" class="form-select select2 @error('warehouse_id') is-invalid @enderror" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                                <input type="date" name="order_date" id="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', date('Y-m-d')) }}" required>
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="expected_delivery_date" class="form-label">Expected Delivery</label>
                                <input type="date" name="expected_delivery_date" id="expected_delivery_date" class="form-control @error('expected_delivery_date') is-invalid @enderror" value="{{ old('expected_delivery_date') }}">
                                @error('expected_delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Sent" {{ old('status') == 'Sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="Delivered" {{ old('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="notify_supplier" id="notify_supplier" value="1" {{ old('notify_supplier') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_supplier">Notify Supplier by Email</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details - Full Width Row -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product / Variant</th>
                                        <th style="width: 150px;">Quantity</th>
                                        <th style="width: 150px;">Unit Cost</th>
                                        <th style="width: 150px;">Subtotal</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <!-- Rows added via JS -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-start fw-bold">
                                            <button type="button" class="btn btn-soft-primary btn-sm" id="addItem">
                                                <i class="bx bx-plus me-1"></i> Add Product
                                            </button>
                                        </td>
                                        <td class="text-end fw-bold">Total Amount:</td>
                                        <td colspan="2">
                                            <input type="text" id="displayTotal" class="form-control fw-bold" readonly value="0.00">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary px-5">Create Purchase Order</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="rowTemplate">
    <tr class="item-row">
        <td>
            <select name="items[INDEX][product_key]" class="form-select product-select select2" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    @if($product->variants->count() > 0)
                        @foreach($product->variants as $variant)
                            <option value="v-{{ $variant->id }}" data-cost="{{ $variant->unit_cost }}" data-product-id="{{ $product->id }}">
                                {{ $product->name }} ({{ $variant->variant_name }})
                            </option>
                        @endforeach
                    @else
                        <option value="p-{{ $product->id }}" data-cost="{{ $product->unit_cost }}" data-product-id="{{ $product->id }}">
                            {{ $product->name }}
                        </option>
                    @endif
                @endforeach
            </select>
            <input type="hidden" name="items[INDEX][product_id]" class="product-id">
            <input type="hidden" name="items[INDEX][product_variant_id]" class="variant-id">
        </td>
        <td>
            <input type="number" name="items[INDEX][order_quantity]" class="form-control quantity" min="1" value="1" required>
        </td>
        <td>
            <input type="number" name="items[INDEX][unit_cost]" class="form-control unit-cost" step="0.01" min="0" required>
        </td>
        <td>
            <input type="text" class="form-control subtotal" readonly value="0.00">
        </td>
        <td>
            <button type="button" class="btn btn-soft-danger btn-sm removeItem">
                <iconify-icon icon="solar:trash-bin-trash-broken" class="align-middle fs-18"></iconify-icon>
            </button>
        </td>
    </tr>
</template>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5' });

        let rowIndex = 0;

        function addRow() {
            let template = $('#rowTemplate').html();
            template = template.replace(/INDEX/g, rowIndex);
            $('#itemsBody').append(template);
            
            // Re-initialize select2 for the new row only
            $('#itemsBody tr').last().find('.select2').select2({ theme: 'bootstrap-5' });
            rowIndex++;
        }

        // Add first row by default
        addRow();

        $('#addItem').on('click', function(e) {
            e.preventDefault();
            addRow();
        });

        $(document).on('click', '.removeItem', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                calculateTotal();
            } else {
                toastr.warning('At least one item is required.');
            }
        });

        $(document).on('change', '.product-select', function() {
            let selectedOption = $(this).find(':selected');
            let selectedValue = $(this).val();
            let row = $(this).closest('tr');
            
            let cost = selectedOption.data('cost') || 0;
            let productId = selectedOption.data('product-id') || '';
            
            row.find('.product-id').val(productId);
            
            if (selectedValue.startsWith('v-')) {
                row.find('.variant-id').val(selectedValue.replace('v-', ''));
            } else {
                row.find('.variant-id').val('');
            }

            row.find('.unit-cost').val(cost);
            calculateRow(row);
        });

        $(document).on('input', '.quantity, .unit-cost', function() {
            calculateRow($(this).closest('tr'));
        });

        function calculateRow(row) {
            let qty = parseFloat(row.find('.quantity').val()) || 0;
            let cost = parseFloat(row.find('.unit-cost').val()) || 0;
            let subtotal = qty * cost;
            row.find('.subtotal').val(subtotal.toFixed(2));
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#displayTotal').val(total.toFixed(2));
        }
    });
</script>
@endsection
