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
                                    <p class="text-muted extra-small mt-1"><i class="bx bx-info-circle me-1"></i>Note: For optimal formatting and clean HTML, it is recommended to prepare your content in <strong>Google Docs</strong> or <strong>Microsoft Word</strong> before pasting.</p>
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
                                    <div class="col-md-4 mb-3">
                                        <label for="min_stock_global" class="form-label text-info">Global Minimum Stock</label>
                                        <input type="number" name="min_stock_global" id="min_stock_global" class="form-control" placeholder="e.g. 10" value="{{ old('min_stock_global', $product->min_stock_global ?? 0) }}">
                                        <p class="text-muted extra-small mt-1">Alert when total stock across all warehouses falls below this.</p>
                                    </div>

                                    <div class="col-md-8 mb-3 base-warehouse-limits-section">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="form-label mb-0 text-primary">Warehouse-Specific Limits</label>
                                            <button type="button" class="btn btn-sm btn-soft-primary add-warehouse-limit-btn" data-is-variant="0">
                                                <i class="bx bx-plus me-1"></i> Add Warehouse
                                            </button>
                                        </div>
                                        <div class="warehouse-limits-list d-flex flex-wrap gap-2">
                                            @php
                                                $currentLimits = old('warehouse_limits', isset($product) ? $product->warehouseStockLimits->whereNull('product_variant_id')->pluck('min_stock', 'warehouse_id')->toArray() : []);
                                            @endphp
                                            @foreach($currentLimits as $whId => $minStock)
                                                @php $warehouse = $warehouses->firstWhere('id', $whId); @endphp
                                                @if($warehouse)
                                                    <div class="warehouse-limit-row badge badge-soft-info d-inline-flex align-items-center gap-2 p-2 me-2 mb-2">
                                                        <span>{{ $warehouse->name }}: <strong>{{ $minStock }}</strong></span>
                                                        <input type="hidden" name="warehouse_limits[{{ $whId }}]" value="{{ $minStock }}">
                                                        <i class="bx bx-x cursor-pointer remove-limit" style="font-size: 16px;"></i>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
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
                                <div class="row">
                                    <!-- Primary Image -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="primary_image" class="form-label">Primary Image <span class="text-danger">*</span></label>
                                            <input type="file" name="primary_image" id="primary_image" class="filepond" {{ !isset($product) ? 'required' : '' }}>
                                            <p class="extra-small text-muted mt-1">This will be the main display image. (Max 2MB)</p>
                                            
                                            @if(isset($product))
                                                @php $primaryImg = $product->images->where('is_primary', true)->first(); @endphp
                                                @if($primaryImg)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/'.$primaryImg->image_path) }}" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: contain;">
                                                    </div>
                                                @endif
                                            @endif
                                            @error('primary_image')
                                                <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Gallery Images -->
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="gallery_images" class="form-label">{{ isset($product) ? 'Add Gallery Images' : 'Gallery Images' }} (Max 5 at a time)</label>
                                            <input type="file" name="gallery_images[]" id="gallery_images" class="filepond" data-allow-reorder="true" data-max-files="5" multiple>
                                            <p class="extra-small text-muted mt-1">Select up to 5 additional images. Individual size must not exceed 2MB.</p>

                                            @if(isset($product) && $product->images->where('is_primary', false)->count() > 0)
                                                <div class="row mt-3">
                                                    @foreach($product->images->where('is_primary', false) as $image)
                                                        <div class="col-md-3 mb-3">
                                                            <div class="position-relative">
                                                                <img src="{{ asset('storage/'.$image->image_path) }}" class="img-thumbnail bg-light" style="height: 80px; width: 100%; object-fit: contain;">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @error('gallery_images')
                                                <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            @error('gallery_images.*')
                                                <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
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
                                                <th>SKU</th>
                                                <th>Price <span class="text-danger">*</span></th>
                                                <th>Disc %</th>
                                                <th style="width: 120px;">Global Limit</th>
                                                <th style="width: 180px;">Wh. Limits</th>
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
                                                    if (is_array($variant)) {
                                                        $vId = $variant['id'] ?? null;
                                                        $vName = $variant['variant_name'] ?? '';
                                                        $vSku = $variant['sku'] ?? '';
                                                        $vPrice = $variant['regular_price'] ?? '';
                                                        $vDisc = $variant['discount_percentage'] ?? '';
                                                        $vLimitGlobal = $variant['min_stock_global'] ?? 0;
                                                        
                                                        $vLimits = collect();
                                                        $rawLimits = $variant['warehouse_limits'] ?? [];
                                                        foreach($rawLimits as $whId => $minStock) {
                                                            $warehouse = \App\Models\Warehouse::find($whId);
                                                            if ($warehouse) {
                                                                $vLimits->push((object)[
                                                                    'warehouse_id' => $whId,
                                                                    'min_stock' => $minStock,
                                                                    'warehouse' => (object)['name' => $warehouse->name]
                                                                ]);
                                                            }
                                                        }
                                                    } else {
                                                        $vId = $variant->id;
                                                        $vName = $variant->variant_name;
                                                        $vSku = $variant->sku;
                                                        $vPrice = $variant->regular_price;
                                                        $vDisc = $variant->discount_percentage;
                                                        $vLimitGlobal = $variant->min_stock_global;
                                                        $vLimits = $variant->warehouseStockLimits;
                                                    }
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
                                                        <input type="number" name="variants[{{ $index }}][min_stock_global]" class="form-control form-control-sm" placeholder="Global" value="{{ $vLimitGlobal }}">
                                                    </td>
                                                    <td class="variant-warehouse-limits-section">
                                                        <div class="d-flex justify-content-end mb-1">
                                                            <button type="button" class="btn btn-extra-small btn-soft-primary add-warehouse-limit-btn" data-is-variant="1" data-row-index="{{ $index }}">
                                                                <i class="bx bx-plus"></i> Add
                                                            </button>
                                                        </div>
                                                        <div class="warehouse-limits-list">
                                                            @foreach($vLimits as $limit)
                                                                <div class="warehouse-limit-row badge badge-soft-info d-flex align-items-center justify-content-between gap-1 p-1 mb-1">
                                                                    <span class="extra-small text-truncate" title="{{ $limit->warehouse->name }}">{{ $limit->warehouse->name }}: {{ $limit->min_stock }}</span>
                                                                    <input type="hidden" name="variants[{{ $index }}][warehouse_limits][{{ $limit->warehouse_id }}]" value="{{ $limit->min_stock }}">
                                                                    <i class="bx bx-x cursor-pointer remove-limit"></i>
                                                                </div>
                                                            @endforeach
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
<!-- Warehouse Limit Modal -->
<div class="modal fade" id="warehouseLimitModal" tabindex="-1" aria-labelledby="warehouseLimitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warehouseLimitModalLabel">Set Warehouse Stock Limit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-target-row-index">
                <input type="hidden" id="modal-is-variant" value="0">
                
                <div class="mb-3">
                    <label for="modal-warehouse-id" class="form-label">Select Warehouse</label>
                    <select id="modal-warehouse-id" class="form-select select2-modal">
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="modal-min-stock" class="form-label">Minimum Stock Limit</label>
                    <input type="number" id="modal-min-stock" class="form-control" placeholder="e.g. 5" min="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-warehouse-limit">Apply Limit</button>
            </div>
        </div>
    </div>
</div>

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
                        <input type="number" name="variants[${variantIndex}][min_stock_global]" class="form-control form-control-sm" placeholder="Global" value="0">
                    </td>
                    <td class="variant-warehouse-limits-section">
                        <div class="d-flex justify-content-end mb-1">
                            <button type="button" class="btn btn-extra-small btn-soft-primary add-warehouse-limit-btn" data-is-variant="1" data-row-index="${variantIndex}">
                                <i class="bx bx-plus"></i> Add
                            </button>
                        </div>
                        <div class="warehouse-limits-list">
                            <!-- Dynamic Limits -->
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

        // Warehouse Limit Modal Logic
        const limitModal = new bootstrap.Modal(document.getElementById('warehouseLimitModal'));
        const modalWhSelect = $('#modal-warehouse-id');
        const modalStockInput = $('#modal-min-stock');
        const modalTargetRowIndex = $('#modal-target-row-index');
        const modalIsVariant = $('#modal-is-variant');

        $(document).on('click', '.add-warehouse-limit-btn', function() {
            const isVariant = $(this).data('is-variant');
            const rowIndex = $(this).data('row-index');
            
            modalIsVariant.val(isVariant);
            modalTargetRowIndex.val(rowIndex);
            modalWhSelect.val('').trigger('change');
            modalStockInput.val('');
            
            limitModal.show();
        });

        $('#save-warehouse-limit').on('click', function() {
            const whId = modalWhSelect.val();
            const whName = modalWhSelect.find(':selected').text();
            const minStock = modalStockInput.val();
            const isVariant = modalIsVariant.val() == '1';
            const rowIndex = modalTargetRowIndex.val();

            if (!whId || !minStock) {
                toastr.error('Please select a warehouse and set a limit.');
                return;
            }

            let container;
            let inputName;

            if (isVariant) {
                container = $(`.variant-row:eq(${rowIndex})`).find('.warehouse-limits-list');
                inputName = `variants[${rowIndex}][warehouse_limits][${whId}]`;
            } else {
                container = $('.base-warehouse-limits-section .warehouse-limits-list');
                inputName = `warehouse_limits[${whId}]`;
            }

            // Check if already exists
            if (container.find(`input[name="${inputName}"]`).length > 0) {
                toastr.warning('This warehouse already has a limit set.');
                return;
            }

            const rowHtml = `
                <div class="warehouse-limit-row badge badge-soft-info d-flex align-items-center justify-content-between gap-1 p-1 mb-1" style="min-width: fit-content;">
                    <span class="extra-small text-truncate" style="max-width: 100px;">${whName}: ${minStock}</span>
                    <input type="hidden" name="${inputName}" value="${minStock}">
                    <i class="bx bx-x cursor-pointer remove-limit"></i>
                </div>
            `;

            container.append(rowHtml);
            limitModal.hide();
        });

        $(document).on('click', '.remove-limit', function() {
            $(this).closest('.warehouse-limit-row').remove();
        });

        $('.select2-modal').select2({
            dropdownParent: $('#warehouseLimitModal'),
            width: '100%',
            theme: 'bootstrap-5'
        });
    });
</script>
@endsection
