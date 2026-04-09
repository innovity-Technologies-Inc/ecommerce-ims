@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">{{ $title }}</h4>
    </div>

    <form action="{{ route('admin.sections.update', $section->section_name) }}" method="POST" enctype="multipart/form-data" id="section-form">
        @csrf
        <div class="row">
            <!-- Left Column: Settings & Selected Products -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Section Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label" for="section_title">Section Title</label>
                                <input type="text" name="section_title" id="section_title" class="form-control" value="{{ old('section_title', $section->section_title) }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="section_subtitle">Section Subtitle</label>
                                <input type="text" name="section_subtitle" id="section_subtitle" class="form-control" value="{{ old('section_subtitle', $section->section_subtitle) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="limit">Product Limit</label>
                                <input type="number" name="limit" id="limit" class="form-control" value="{{ old('limit', $section->limit) }}" required min="1" max="50">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">Selection Mode</label>
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input" type="radio" name="mode" id="mode_organic" value="organic" {{ old('mode', $section->mode) === 'organic' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mode_organic">Organic</label>
                                </div>
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input" type="radio" name="mode" id="mode_custom" value="custom" {{ old('mode', $section->mode) === 'custom' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mode_custom">Custom</label>
                                </div>
                            </div>

                            @if($section->section_name === 'featured')
                            <div class="col-md-12">
                                <label for="background_image" class="form-label">Background Image</label>
                                <input type="file" name="background_image" id="background_image" class="filepond">
                                @if($section->background_image)
                                    <div class="mt-2 text-center bg-light p-2 rounded">
                                        <img src="{{ asset('storage/'.$section->background_image) }}" alt="background" class="img-fluid rounded" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                            @endif

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_visible" id="is_visible" {{ old('is_visible', $section->is_visible) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_visible">Show Section on Homepage</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card {{ old('mode', $section->mode) === 'custom' ? '' : 'd-none' }}" id="custom-products-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Selected Products</h5>
                        <span class="badge bg-primary" id="selected-count">{{ $selectedProducts->count() }} Products</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="selected-products-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="selected-products-body">
                                    @foreach($selectedProducts as $index => $product)
                                    <tr data-product-id="{{ $product->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $imagePath = $product->primaryImage ? $product->primaryImage->image_path : 'admin_assets/assets/images/logo-sm.png';
                                                    $imageUrl = $product->primaryImage ? asset('storage/' . $imagePath) : asset($imagePath);
                                                    $priceData = \App\HelperClass::getProductPriceRange($product);
                                                    $gs = \App\HelperClass::generalSettings();
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 text-truncate" style="max-width: 250px;">{{ $product->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $gs->currency ?? '$' }}{{ number_format($priceData['min'], 2) }}
                                                    </small>
                                                </div>
                                            </div>
                                            <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-product"><i class="bx bx-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($selectedProducts->isEmpty())
                                    <tr id="no-products-msg">
                                        <td colspan="2" class="text-center py-4 text-muted">No products selected. Search and add from the right panel.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Save All Changes</button>
                </div>
            </div>

            <!-- Right Column: Product Selector -->
            <div class="col-lg-6 {{ old('mode', $section->mode) === 'custom' ? '' : 'd-none' }}" id="product-selector-column">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Selector</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" id="product-search-input" class="form-control" placeholder="Search by name or SKU...">
                                    <button class="btn btn-outline-secondary" type="button" id="search-btn"><i class="bx bx-search"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select id="category-filter" class="form-select form-select-sm filter-input">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="subcategory-filter" class="form-select form-select-sm filter-input">
                                    <option value="">Sub Category</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="brand-filter" class="form-select form-select-sm filter-input">
                                    <option value="">All Brands</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select id="sort-filter" class="form-select form-select-sm filter-input">
                                    <option value="latest">Sort: Latest</option>
                                    <option value="oldest">Sort: Oldest</option>
                                    <option value="a-z">Sort: A to Z</option>
                                    <option value="z-a">Sort: Z to A</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="reset-selector" class="btn btn-sm btn-outline-danger w-100">Reset</button>
                            </div>
                        </div>

                        <div id="product-list-container" style="max-height: 650px; overflow-y: auto;">
                            <!-- AJAX content here -->
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const productListContainer = $('#product-list-container');
        const selectedProductsBody = $('#selected-products-body');
        const noProductsMsg = $('#no-products-msg');
        const selectedCount = $('#selected-count');
        const categories = @json($categories);

        // Handle Mode Switch
        $('input[name="mode"]').on('change', function() {
            if ($(this).val() === 'custom') {
                $('#custom-products-card').removeClass('d-none');
                $('#product-selector-column').removeClass('d-none');
                fetchProducts();
            } else {
                $('#custom-products-card').addClass('d-none');
                $('#product-selector-column').addClass('d-none');
            }
        });

        // Populate Subcategories
        $('#category-filter').on('change', function() {
            const categoryId = $(this).val();
            const subcategorySelect = $('#subcategory-filter');
            subcategorySelect.html('<option value="">Sub Category</option>');
            
            if (categoryId) {
                const category = categories.find(c => c.id == categoryId);
                if (category && category.subcategories && category.subcategories.length > 0) {
                    category.subcategories.forEach(sub => {
                        subcategorySelect.append(`<option value="${sub.id}">${sub.name}</option>`);
                    });
                }
            }
            fetchProducts();
        });

        // Search products
        function fetchProducts(url = "{{ route('admin.sections.search_products') }}") {
            const search = $('#product-search-input').val();
            const categoryId = $('#category-filter').val();
            const subCategoryId = $('#subcategory-filter').val();
            const brandId = $('#brand-filter').val();
            const sort = $('#sort-filter').val();
            
            productListContainer.css('opacity', '0.5');

            $.ajax({
                url: url,
                type: 'GET',
                data: { 
                    search: search,
                    category_id: categoryId,
                    sub_category_id: subCategoryId,
                    brand_id: brandId,
                    sort: sort
                },
                success: function(response) {
                    productListContainer.html(response);
                    productListContainer.css('opacity', '1');
                },
                error: function() {
                    productListContainer.css('opacity', '1');
                    toastr.error('Failed to fetch products');
                }
            });
        }

        $('#product-search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchProducts, 500);
        });

        $('#search-btn').on('click', fetchProducts);
        $('.filter-input').on('change', fetchProducts);

        $('#reset-selector').on('click', function() {
            $('#product-search-input').val('');
            $('#category-filter').val('');
            $('#subcategory-filter').html('<option value="">Sub Category</option>');
            $('#brand-filter').val('');
            $('#sort-filter').val('latest');
            fetchProducts();
        });

        // Add product to selection
        $(document).on('click', '.add-product-btn', function() {
            const btn = $(this);
            const productId = btn.data('id');
            const productName = btn.data('name');
            const productPrice = btn.data('price');
            const productImage = btn.data('image');

            // Check if already added
            if ($(`#selected-products-body tr[data-product-id="${productId}"]`).length > 0) {
                toastr.warning('Product already added');
                return;
            }

            if ($('#no-products-msg').length) $('#no-products-msg').remove();

            const html = `
                <tr data-product-id="${productId}">
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${productImage}" alt="" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0 text-truncate" style="max-width: 250px;">${productName}</h6>
                                <small class="text-muted">Price: {{ config('app.currency', '$') }}${parseFloat(productPrice).toFixed(2)}</small>
                            </div>
                        </div>
                        <input type="hidden" name="product_ids[]" value="${productId}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-product"><i class="bx bx-trash"></i></button>
                    </td>
                </tr>
            `;

            selectedProductsBody.append(html);
            updateCount();
            toastr.success('Product added');
        });

        // Remove product
        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            updateCount();
            
            if (selectedProductsBody.find('tr[data-product-id]').length === 0) {
                if ($('#no-products-msg').length === 0) {
                    selectedProductsBody.append(`
                        <tr id="no-products-msg">
                            <td colspan="2" class="text-center py-4 text-muted">No products selected. Search and add from the right panel.</td>
                        </tr>
                    `);
                }
            }
        });

        function updateCount() {
            const count = selectedProductsBody.find('tr[data-product-id]').length;
            selectedCount.text(`${count} Products`);
        }

        // Handle pagination clicks in product list
        $(document).on('click', '#product-list-container .pagination a', function(e) {
            e.preventDefault();
            fetchProducts($(this).attr('href'));
        });

        // Initial fetch on page load if mode is custom
        if ($('input[name="mode"]:checked').val() === 'custom') {
            fetchProducts();
        }
    });
</script>
@endsection
