@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Product List</h4>
        <div>
            <a href="{{ route('admin.products.import') }}" class="btn btn-outline-info btn-sm me-1">
                <i class="bx bx-import me-1"></i>Import
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus me-1"></i>Add
            </a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="row g-3">
                <!-- First Row: Search, Category, Sub Category -->
                <div class="col-lg-4">
                    <label class="form-label">Search</label>
                    <div class="search-box">
                        <input type="text" class="form-control" id="search-input" placeholder="Search products..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">Category</label>
                    <select class="form-select filter-select" id="category-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">Sub Category</label>
                    <select class="form-select filter-select" id="subcategory-select">
                        <option value="">All Sub Categories</option>
                    </select>
                </div>

                <!-- Second Row: Brand, Status, Sort, Reset -->
                <div class="col-lg-3">
                    <label class="form-label">Brand</label>
                    <select class="form-select filter-select" id="brand-select">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Status</label>
                    <select class="form-select filter-select" id="status-select">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Sort</label>
                    <select class="form-select filter-select" id="sort-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A to Z</option>
                        <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Z to A</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100" id="reset-filters">Reset Filters</button>
                </div>
            </div>
        </div>
        <div class="card-body p-0" id="table-container">
            @include('admin.products.partials.table')
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const tableContainer = $('#table-container');
        const categories = @json($categories);

        function populateSubcategories(categoryId, selectedSubId = null) {
            const subcategorySelect = $('#subcategory-select');
            subcategorySelect.html('<option value="">All Sub Categories</option>');
            
            if (categoryId) {
                const category = categories.find(c => c.id == categoryId);
                if (category && category.subcategories && category.subcategories.length > 0) {
                    category.subcategories.forEach(sub => {
                        const isSelected = selectedSubId && sub.id == selectedSubId ? 'selected' : '';
                        subcategorySelect.append(`<option value="${sub.id}" ${isSelected}>${sub.name}</option>`);
                    });
                }
            }
        }

        // Initialize subcategories if category is selected
        const initialCategoryId = $('#category-select').val();
        if (initialCategoryId) {
            populateSubcategories(initialCategoryId, "{{ request('sub_category_id') }}");
        }

        $('#category-select').on('change', function() {
            populateSubcategories($(this).val());
            fetchProducts();
        });

        function fetchProducts() {
            const search = $('#search-input').val();
            const categoryId = $('#category-select').val();
            const subCategoryId = $('#subcategory-select').val();
            const brandId = $('#brand-select').val();
            const status = $('#status-select').val();
            const sort = $('#sort-select').val();
            
            // Add loading state
            tableContainer.css('opacity', '0.5');

            $.ajax({
                url: "{{ route('admin.products.index') }}",
                type: 'GET',
                data: {
                    search: search,
                    category_id: categoryId,
                    sub_category_id: subCategoryId,
                    brand_id: brandId,
                    status: status,
                    sort: sort
                },
                success: function(response) {
                    tableContainer.html(response);
                    tableContainer.css('opacity', '1');
                    
                    // Update URL without refresh
                    const url = new URL(window.location.href);
                    if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
                    if (categoryId) url.searchParams.set('category_id', categoryId); else url.searchParams.delete('category_id');
                    if (subCategoryId) url.searchParams.set('sub_category_id', subCategoryId); else url.searchParams.delete('sub_category_id');
                    if (brandId) url.searchParams.set('brand_id', brandId); else url.searchParams.delete('brand_id');
                    if (status !== '') url.searchParams.set('status', status); else url.searchParams.delete('status');
                    if (sort) url.searchParams.set('sort', sort); else url.searchParams.delete('sort');
                    window.history.pushState({}, '', url);
                },
                error: function() {
                    tableContainer.css('opacity', '1');
                    toastr.error('Failed to fetch products');
                }
            });
        }

        $('#search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchProducts, 500);
        });

        $('.filter-select').on('change', fetchProducts);

        $('#reset-filters').on('click', function() {
            $('#search-input').val('');
            $('#category-select').val('');
            $('#subcategory-select').html('<option value="">All Sub Categories</option>');
            $('#brand-select').val('');
            $('#status-select').val('');
            $('#sort-select').val('latest');
            fetchProducts();
        });

        // Handle pagination clicks
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            
            tableContainer.css('opacity', '0.5');
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    tableContainer.html(response);
                    tableContainer.css('opacity', '1');
                    window.history.pushState({}, '', url);
                }
            });
        });
    });
</script>
@endsection
