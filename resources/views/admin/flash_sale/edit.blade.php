@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Manage Flash Sale</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.flash_sale.update') }}" method="POST" id="flash-sale-form">
        @csrf
        <div class="row">
            <!-- Left Column: Settings & Selected Products -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Flash Sale Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Sale Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $flashSale->name) }}" placeholder="e.g. Summer Flash Sale">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select">
                                    <option value="1" {{ old('status', $flashSale->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $flashSale->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                @php
                                    $endDateValue = old('end_date');
                                    if ($endDateValue) {
                                        try {
                                            $endDateValue = \Carbon\Carbon::parse($endDateValue)->format('Y-m-d\TH:i');
                                        } catch (\Exception $e) {
                                            $endDateValue = '';
                                        }
                                    } elseif ($flashSale->end_date) {
                                        $endDateValue = $flashSale->end_date->format('Y-m-d\TH:i');
                                    } else {
                                        $endDateValue = '';
                                    }
                                @endphp
                                <input type="datetime-local" name="end_date" class="form-control" value="{{ $endDateValue }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Selected Products</h5>
                        <span class="badge bg-primary" id="selected-count">{{ $flashSale->items->count() }} Products</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="selected-products-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th style="width: 120px;">Discount <span class="text-danger">*</span></th>
                                        <th style="width: 100px;">Type <span class="text-danger">*</span></th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="selected-products-body">
                                    @foreach($flashSale->items as $index => $item)
                                    <tr data-product-id="{{ $item->product_id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $imagePath = $item->product->primaryImage ? $item->product->primaryImage->image_path : 'admin_assets/assets/images/logo-sm.png';
                                                    $imageUrl = $item->product->primaryImage ? asset('storage/' . $imagePath) : asset($imagePath);
                                                    $priceData = \App\HelperClass::getProductPriceRange($item->product);
                                                    $gs = \App\HelperClass::generalSettings();
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $item->product->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $gs->currency ?? '$' }}{{ number_format($priceData['min'], 2) }}
                                                        @if($priceData['has_range'])
                                                            - {{ $gs->currency ?? '$' }}{{ number_format($priceData['max'], 2) }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                            <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                        </td>
                                        <td>
                                            <input type="number" name="products[{{ $index }}][discount_amount]" class="form-control form-control-sm" value="{{ $item->discount_amount }}" step="0.01" min="0">
                                        </td>
                                        <td>
                                            <select name="products[{{ $index }}][discount_type]" class="form-select form-select-sm">
                                                <option value="percentage" {{ $item->discount_type == 'percentage' ? 'selected' : '' }}>%</option>
                                                <option value="fixed" {{ $item->discount_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-product"><i class="bx bx-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($flashSale->items->isEmpty())
                                    <tr id="no-products-msg">
                                        <td colspan="4" class="text-center py-4 text-muted">No products selected. Search and add from the right panel.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        @can('flash_sale.edit')
                        <button type="submit" class="btn btn-primary w-100">Save Flash Sale Changes</button>
                        @endcan
                    </div>
                </div>
            </div>

            @can('flash_sale.edit')
            <!-- Right Column: Product Selector -->
            <div class="col-lg-6">
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
                                <select id="category-filter" class="form-select form-select-sm">
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

                        <div id="product-list-container" style="max-height: 550px; overflow-y: auto;">
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
            @else
            <div class="col-lg-6">
                <div class="alert alert-info">
                    You do not have permission to modify flash sale products.
                </div>
            </div>
            @endcan
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        let productIndex = {{ $flashSale->items->count() }};
        const productListContainer = $('#product-list-container');
        const selectedProductsBody = $('#selected-products-body');
        const noProductsMsg = $('#no-products-msg');
        const selectedCount = $('#selected-count');
        const categories = @json($categories);

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
        function fetchProducts(url = "{{ route('admin.flash_sale.search_products') }}") {
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
                                <h6 class="mb-0 text-truncate" style="max-width: 150px;">${productName}</h6>
                                <small class="text-muted">Price: {{ config('app.currency', '$') }}${parseFloat(productPrice).toFixed(2)}</small>
                            </div>
                        </div>
                        <input type="hidden" name="products[${productIndex}][product_id]" value="${productId}">
                    </td>
                    <td>
                        <input type="number" name="products[${productIndex}][discount_amount]" class="form-control form-control-sm" value="0" step="0.01" min="0">
                    </td>
                    <td>
                        <select name="products[${productIndex}][discount_type]" class="form-select form-select-sm">
                            <option value="percentage">%</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-product"><i class="bx bx-trash"></i></button>
                    </td>
                </tr>
            `;

            selectedProductsBody.append(html);
            productIndex++;
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
                            <td colspan="4" class="text-center py-4 text-muted">No products selected. Search and add from the right panel.</td>
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

        // Initial fetch on page load
        fetchProducts();
    });
</script>
@endsection
