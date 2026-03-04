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
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ isset($product) ? 'Edit Product: '.$product->name : 'Create New Product' }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Form Part 1: Category and Sub-category selection -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
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
                                    <label for="name" class="form-label">Product Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Product Name" value="{{ old('name', $product->name ?? '') }}" required>
                                    @error('name')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="editor1" class="form-label">Description</label>
                                    <textarea name="description" id="editor1" class="form-control summernote">{{ old('description', $product->description ?? '') }}</textarea>
                                    @error('description')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
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
                            </div>

                            <!-- Image Upload Section -->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="images" class="form-label">{{ isset($product) ? 'Add More Images' : 'Product Images' }} (Multiple Selectable)</label>
                                    <input type="file" name="images[]" id="images" class="filepond" multiple>
                                    <p class="small text-muted mt-1">Select one or more images. {{ !isset($product) ? 'The first image will be set as primary.' : '' }}</p>
                                    
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
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>SKU (Optional)</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $defaultVariants = [['size' => '', 'color' => '', 'sku' => '', 'price' => '', 'stock' => '']];
                                                $oldVariants = old('variants', isset($product) ? $product->variants->toArray() : $defaultVariants);
                                            @endphp
                                            @foreach($oldVariants as $index => $variant)
                                                <tr class="variant-row">
                                                    <td><input type="text" name="variants[{{ $index }}][size]" class="form-control" placeholder="e.g. XL" value="{{ $variant['size'] ?? '' }}"></td>
                                                    <td><input type="text" name="variants[{{ $index }}][color]" class="form-control" placeholder="e.g. Red" value="{{ $variant['color'] ?? '' }}"></td>
                                                    <td><input type="text" name="variants[{{ $index }}][sku]" class="form-control" placeholder="Auto-generated if empty" value="{{ $variant['sku'] ?? '' }}"></td>
                                                    <td><input type="number" step="0.01" name="variants[{{ $index }}][price]" class="form-control" placeholder="0.00" value="{{ $variant['price'] ?? '' }}" required></td>
                                                    <td><input type="number" name="variants[{{ $index }}][stock]" class="form-control" placeholder="e.g. 100" value="{{ $variant['stock'] ?? '' }}"></td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-row" {{ count($oldVariants) <= 1 ? 'disabled' : '' }}><i class="bx bx-trash"></i></button>
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

        // Dynamic Variant UI
        let variantIndex = {{ isset($oldVariants) ? count($oldVariants) : 1 }};
        $('#add-variant-row').on('click', function() {
            const newRow = `
                <tr class="variant-row">
                    <td><input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="e.g. XL"></td>
                    <td><input type="text" name="variants[${variantIndex}][color]" class="form-control" placeholder="e.g. Red"></td>
                    <td><input type="text" name="variants[${variantIndex}][sku]" class="form-control" placeholder="Auto-generated if empty"></td>
                    <td><input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control" placeholder="0.00" required></td>
                    <td><input type="number" name="variants[${variantIndex}][stock]" class="form-control" placeholder="e.g. 100"></td>
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
