@extends('admin.structure.app')
@section('content')

<!-- Start Container Fluid -->
<div class="container-xxl">

    <div class="row">
        <div class="col-lg-12">
            <form action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                @if(isset($product))
                    @method('put')
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ isset($product) ? 'Edit Product: '.$product->name : 'Create New Product' }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Form Part 1: Category and Sub-category selection -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-control select2_list" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }} data-subcategories="{{ json_encode($category->subcategories) }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="sub_category_id" class="form-label">Sub Category</label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-control select2_list">
                                        <option value="">Select Sub Category</option>
                                    </select>
                                    @error('sub_category_id')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="brand_id" class="form-label">Brand</label>
                                    <select name="brand_id" id="brand_id" class="form-control select2_list">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Part 2: Base product details -->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Product Name" value="{{ old('name', $product->name ?? '') }}" required>
                                    @error('name')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="short_description" class="form-label">Short Description</label>
                                    <textarea name="short_description" id="short_description" class="form-control" rows="3" placeholder="Enter short description for product detail page...">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                                    @error('short_description')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="editor1" class="form-label">Main Description</label>
                                    <textarea name="description" id="editor1" class="form-control summernote">{{ old('description', $product->description ?? '') }}</textarea>
                                    @error('description')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12 mb-3">
                                <label class="form-label d-block">Pricing Type</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input pricing-type-radio" type="radio" name="pricing_type" id="type_base" value="base" {{ !isset($product) || $product->regular_price ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_base">Base Pricing (Single Price for all Variants)</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input pricing-type-radio" type="radio" name="pricing_type" id="type_variant" value="variant" {{ isset($product) && !$product->regular_price ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_variant">Variant Pricing (Specific Price per Variant)</label>
                                </div>
                            </div>

                            <div class="col-lg-6 base-price-section" style="{{ isset($product) && !$product->regular_price ? 'display:none;' : '' }}">
                                <div class="mb-3">
                                    <label for="regular_price" class="form-label">Base Regular Price <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="regular_price" id="regular_price" class="form-control" placeholder="0.00" value="{{ old('regular_price', $product->regular_price ?? '') }}">
                                    @error('regular_price')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6 base-price-section" style="{{ isset($product) && !$product->regular_price ? 'display:none;' : '' }}">
                                <div class="mb-3">
                                    <label for="discount_percentage" class="form-label">Base Discount %</label>
                                    <input type="number" name="discount_percentage" id="discount_percentage" class="form-control" placeholder="e.g. 10" value="{{ old('discount_percentage', $product->discount_percentage ?? '') }}">
                                    @error('discount_percentage')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12 base-price-section" style="{{ isset($product) && !$product->regular_price ? 'display:none;' : '' }}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label d-block">Stock Limit Type</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input min-stock-type-radio" type="radio" name="min_stock_type" id="min_stock_type_global" value="global" {{ old('min_stock_type', $product->min_stock_type ?? 'global') == 'global' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="min_stock_type_global">Global</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input min-stock-type-radio" type="radio" name="min_stock_type" id="min_stock_type_warehouse" value="warehouse" {{ old('min_stock_type', $product->min_stock_type ?? 'global') == 'warehouse' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="min_stock_type_warehouse">Warehouse</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3 global-min-stock-section" style="{{ old('min_stock_type', $product->min_stock_type ?? 'global') == 'warehouse' ? 'display:none;' : '' }}">
                                        <label for="min_stock_global" class="form-label">Global Minimum Stock</label>
                                        <input type="number" name="min_stock_global" id="min_stock_global" class="form-control" placeholder="e.g. 10" value="{{ old('min_stock_global', $product->min_stock_global ?? 0) }}">
                                    </div>

                                    <div class="col-12 warehouse-min-stock-section" style="{{ old('min_stock_type', $product->min_stock_type ?? 'global') == 'global' ? 'display:none;' : '' }}">
                                        @if(isset($product) && $product->inventoryLevels->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Warehouse</th>
                                                            <th>Current Stock</th>
                                                            <th>Min Stock Override</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($product->inventoryLevels as $level)
                                                            <tr>
                                                                <td>{{ $level->warehouse->name }}</td>
                                                                <td>{{ $level->current_quantity }}</td>
                                                                <td>
                                                                    <input type="number" name="inventory_overrides[{{ $level->id }}]" class="form-control form-control-sm" value="{{ old('inventory_overrides.'.$level->id, $level->min_stock_override) }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted small">No inventory records found for this product. Minimum stock can only be set per-warehouse after stock is allocated.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Product Flags -->
                            <div class="col-lg-12 mb-3">
                                <label class="form-label d-block">Product Flags</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="is_new_arrival" id="is_new_arrival" value="1" {{ old('is_new_arrival', $product->is_new_arrival ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_new_arrival">Newly Arrival</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="is_hot_deal" id="is_hot_deal" value="1" {{ old('is_hot_deal', $product->is_hot_deal ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_hot_deal">Hot Deals</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Featured</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="is_top_pick" id="is_top_pick" value="1" {{ old('is_top_pick', $product->is_top_pick ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_top_pick">Top Picks</label>
                                </div>
                            </div>

                            <!-- Product Status -->
                            <div class="col-lg-12 mb-3">
                                <label class="form-label d-block">Product Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $product->status ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Active (Uncheck to discontinue)</label>
                                </div>
                            </div>

                            <!-- Image Upload Section -->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="images" class="form-label">{{ isset($product) ? 'Add More Images' : 'Product Images' }} (Multiple Selectable)</label>
                                    <input type="file" name="images[]" id="images" class="filepond" multiple>
                                    <p class="small text-danger mt-1 fw-bold">Individual image size must not exceed 600 KB. Allowed formats: JPEG, PNG, JPG, GIF, SVG, WEBP.</p>
                                    <p class="small text-muted">Select one or more images. {{ !isset($product) ? 'The first image will be set as primary.' : '' }}</p>

                                    @if(isset($product) && $product->images->count() > 0)
                                        <div class="row mt-3">
                                            @foreach($product->images as $image)
                                                <div class="col-md-2 mb-3">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/'.$image->image_path) }}" class="img-thumbnail bg-light" style="height: 100px; width: 100%; object-fit: contain;">
                                                        @if($image->is_primary)
                                                            <span class="badge bg-primary position-absolute top-0 start-0">Primary</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @error('images')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Part 3: Variation List -->
                            <div class="col-lg-12 mt-4">
                                <h5 class="mb-3">Product Variations</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="variant-table">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Variant Name <span class="text-danger">*</span></th>
                                                <th>SKU (Optional)</th>
                                                <th>Regular Price <span class="text-danger">*</span></th>
                                                <th>Discount %</th>
                                                <th>Stock Limit</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $defaultVariants = [];
                                                $oldVariants = old('variants', isset($product) ? $product->variants : $defaultVariants);
                                            @endphp
                                            @foreach($oldVariants as $index => $variant)
                                                @php
                                                    // Handle both array (old input) and model (edit)
                                                    $vId = is_array($variant) ? ($variant['id'] ?? null) : $variant->id;
                                                    $vName = is_array($variant) ? $variant['variant_name'] : $variant->variant_name;
                                                    $vSku = is_array($variant) ? $variant['sku'] : $variant->sku;
                                                    $vPrice = is_array($variant) ? $variant['regular_price'] : $variant->regular_price;
                                                    $vDisc = is_array($variant) ? $variant['discount_percentage'] : $variant->discount_percentage;
                                                    $vLimitType = is_array($variant) ? ($variant['min_stock_type'] ?? 'global') : $variant->min_stock_type;
                                                    $vLimitGlobal = is_array($variant) ? ($variant['min_stock_global'] ?? 0) : $variant->min_stock_global;
                                                    
                                                    $vLevels = !is_array($variant) ? $variant->inventoryLevels : collect();
                                                @endphp
                                                <tr class="variant-row">
                                                    @if($vId)
                                                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $vId }}">
                                                    @endif
                                                    <td><input type="text" name="variants[{{ $index }}][variant_name]" class="form-control" placeholder="e.g. Blue - L" value="{{ $vName }}" required></td>
                                                    <td><input type="text" name="variants[{{ $index }}][sku]" class="form-control" placeholder="Auto-generated" value="{{ $vSku }}"></td>
                                                    <td><input type="number" step="0.01" name="variants[{{ $index }}][regular_price]" class="form-control variant-price-input" placeholder="0.00" value="{{ $vPrice }}"></td>
                                                    <td><input type="number" name="variants[{{ $index }}][discount_percentage]" class="form-control variant-discount-input" placeholder="e.g. 10" value="{{ $vDisc }}"></td>
                                                    <td>
                                                        <div class="mb-2">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input v-min-stock-type" type="radio" name="variants[{{ $index }}][min_stock_type]" value="global" {{ $vLimitType == 'global' ? 'checked' : '' }}>
                                                                <label class="form-check-label small">Global</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input v-min-stock-type" type="radio" name="variants[{{ $index }}][min_stock_type]" value="warehouse" {{ $vLimitType == 'warehouse' ? 'checked' : '' }}>
                                                                <label class="form-check-label small">Warehouse</label>
                                                            </div>
                                                        </div>
                                                        <div class="v-global-limit" style="{{ $vLimitType == 'warehouse' ? 'display:none;' : '' }}">
                                                            <input type="number" name="variants[{{ $index }}][min_stock_global]" class="form-control form-control-sm" placeholder="Global Limit" value="{{ $vLimitGlobal }}">
                                                        </div>
                                                        <div class="v-warehouse-limit" style="{{ $vLimitType == 'global' ? 'display:none;' : '' }}">
                                                            @if($vLevels->count() > 0)
                                                                @foreach($vLevels as $level)
                                                                    <div class="input-group input-group-sm mb-1">
                                                                        <span class="input-group-text px-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis;" title="{{ $level->warehouse->name }}">{{ $level->warehouse->name }}</span>
                                                                        <input type="number" name="variants[{{ $index }}][inventory_overrides][{{ $level->id }}]" class="form-control" placeholder="Limit" value="{{ old('variants.'.$index.'.inventory_overrides.'.$level->id, $level->min_stock_override) }}">
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted extra-small">No inventory records</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bx bx-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-soft-primary btn-sm mt-2" id="add-variant-row">
                                    <i class="bx bx-plus me-1"></i> Add Variation Row
                                </button>
                                @error('variants')
                                <div class="small text-danger mt-2">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top">
                        <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update Product' : 'Create Product' }}</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const categorySelect = $('#category_id');
        const subCategorySelect = $('#sub_category_id');
        const oldSubCategoryId = "{{ old('sub_category_id', $product->sub_category_id ?? '') }}";

        function updateSubcategories(subcategories, selectedSubId) {
            subCategorySelect.empty().append('<option value="">Select Sub Category</option>');

            if (subcategories && subcategories.length > 0) {
                subcategories.forEach(sub => {
                    const isSelected = (selectedSubId && selectedSubId == sub.id) ? 'selected' : '';
                    subCategorySelect.append(`<option value="${sub.id}" ${isSelected}>${sub.name}</option>`);
                });
            }

            // Refresh Select2 display
            if (subCategorySelect.hasClass('select2-hidden-accessible')) {
                subCategorySelect.trigger('change.select2');
            } else {
                subCategorySelect.select2({
                    width: '100%',
                    theme: 'bootstrap-5'
                });
            }
        }

        // Handle category change
        categorySelect.on('change', function(e) {
            const selectedOption = $(this).find(':selected');
            const subcategories = selectedOption.data('subcategories');
            updateSubcategories(subcategories, null);
        });

        // Initial load for existing values
        const initialCategoryId = categorySelect.val();
        if (initialCategoryId) {
            // Give a small delay for Select2 to be ready if it's initialized globally
            setTimeout(() => {
                const selectedOption = categorySelect.find(':selected');
                const initialSubcategories = selectedOption.data('subcategories');
                if (initialSubcategories) {
                    updateSubcategories(initialSubcategories, oldSubCategoryId);
                }
            }, 300);
        }

        // Pricing Type Toggle
        $('.pricing-type-radio').on('change', function() {
            if ($(this).val() === 'base') {
                $('.base-price-section').show();
                $('.variant-price-input, .variant-discount-input').prop('disabled', true).val('');
            } else {
                $('.base-price-section').hide();
                $('.variant-price-input, .variant-discount-input').prop('disabled', false);
            }
        });

        // Trigger initial state
        $('.pricing-type-radio:checked').trigger('change');

        // Stock Limit Type Toggle (Base Product)
        $(document).on('change', '.min-stock-type-radio', function() {
            const tr = $(this).closest('.row');
            if ($(this).val() === 'global') {
                tr.find('.global-min-stock-section').show();
                tr.find('.warehouse-min-stock-section').hide();
            } else {
                tr.find('.global-min-stock-section').hide();
                tr.find('.warehouse-min-stock-section').show();
            }
        });

        // Stock Limit Type Toggle (Variants)
        $(document).on('change', '.v-min-stock-type', function() {
            const td = $(this).closest('td');
            if ($(this).val() === 'global') {
                td.find('.v-global-limit').show();
                td.find('.v-warehouse-limit').hide();
            } else {
                td.find('.v-global-limit').hide();
                td.find('.v-warehouse-limit').show();
            }
        });

        // Dynamic Variant UI
        let variantIndex = {{ isset($oldVariants) ? count($oldVariants) : 0 }};
        $('#add-variant-row').on('click', function() {
            const isBasePricing = $('#type_base').is(':checked');
            const newRow = `
                <tr class="variant-row">
                    <td><input type="text" name="variants[${variantIndex}][variant_name]" class="form-control" placeholder="e.g. Blue - L" required></td>
                    <td><input type="text" name="variants[${variantIndex}][sku]" class="form-control" placeholder="Auto-generated"></td>
                    <td><input type="number" step="0.01" name="variants[${variantIndex}][regular_price]" class="form-control variant-price-input" placeholder="0.00" ${isBasePricing ? 'disabled' : 'required'}></td>
                    <td><input type="number" name="variants[${variantIndex}][discount_percentage]" class="form-control variant-discount-input" placeholder="e.g. 10" ${isBasePricing ? 'disabled' : ''}></td>
                    <td>
                        <div class="mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input v-min-stock-type" type="radio" name="variants[${variantIndex}][min_stock_type]" value="global" checked>
                                <label class="form-check-label small">Global</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input v-min-stock-type" type="radio" name="variants[${variantIndex}][min_stock_type]" value="warehouse">
                                <label class="form-check-label small">Warehouse</label>
                            </div>
                        </div>
                        <div class="v-global-limit">
                            <input type="number" name="variants[${variantIndex}][min_stock_global]" class="form-control form-control-sm" placeholder="Global Limit" value="0">
                        </div>
                        <div class="v-warehouse-limit" style="display:none;">
                            <span class="text-muted extra-small">Save to see warehouses</span>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bx bx-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#variant-table tbody').append(newRow);
            variantIndex++;
            $('.remove-row').prop('disabled', false);
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            if ($('.variant-row').length === 1) {
                $('.remove-row').prop('disabled', true);
            }
        });
    });
</script>
@endsection
