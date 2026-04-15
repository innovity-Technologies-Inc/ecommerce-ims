@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Product Details</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Edit Product">
                <i class="bx bx-edit fs-16"></i>
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    @if($product->primaryImage)
                        <img id="main-product-image" src="{{ asset('storage/'.$product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 300px;">
                            <span class="text-muted">No Image</span>
                        </div>
                    @endif
                    <div class="row g-2">
                        @foreach($product->images as $image)
                            <div class="col-3">
                                <img src="{{ asset('storage/'.$image->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-fluid rounded border thumbnail-image {{ $image->is_primary ? 'border-primary border-2' : '' }} bg-light" 
                                     style="cursor: pointer; height: 60px; width: 100%; object-fit: contain;"
                                     data-full-image="{{ asset('storage/'.$image->image_path) }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $product->name }}</h4>
                    <div class="d-flex gap-1">
                        @if($product->is_new_arrival)
                            <span class="badge bg-success">Newly Arrival</span>
                        @endif
                        @if($product->is_hot_deal)
                            <span class="badge bg-danger">Hot Deal</span>
                        @endif
                        @if($product->is_featured)
                            <span class="badge bg-primary">Featured</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Category:</div>
                        <div class="col-sm-9 text-muted">{{ $product->category->name ?? '-' }} / {{ $product->subCategory->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Brand:</div>
                        <div class="col-sm-9 text-muted">{{ $product->brand->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Slug:</div>
                        <div class="col-sm-9 text-muted">{{ $product->slug }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Base Price:</div>
                        <div class="col-sm-9 text-muted">
                            @php($gs = \App\HelperClass::generalSettings())
                            @if($product->discount_price > 0)
                                <span class="text-decoration-line-through text-muted small">{{ $gs->currency ?? '$' }}{{ number_format($product->regular_price, 2) }}</span>
                                <span class="text-danger fw-bold ms-1">{{ $gs->currency ?? '$' }}{{ number_format($product->discount_price, 2) }}</span>
                                <span class="badge bg-soft-danger text-danger ms-1">-{{ $product->discount_percentage }}%</span>
                            @else
                                {{ $gs->currency ?? '$' }}{{ number_format($product->regular_price ?? 0, 2) }}
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Saleable Stock:</div>
                        <div class="col-sm-9 text-muted"><span class="badge badge-soft-dark fs-13">{{ $product->stock ?? 0 }} Units</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Global Stock Limit:</div>
                        <div class="col-sm-9 text-muted">
                            @if($product->min_stock_global > 0)
                                <span class="badge bg-info">{{ $product->min_stock_global }}</span>
                            @else
                                <span class="text-muted small italic">Not set</span>
                            @endif
                        </div>
                    </div>

                    @if($product->warehouseStockLimits->whereNull('product_variant_id')->count() > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">Warehouse Limits:</div>
                            <div class="col-sm-9">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Warehouse</th>
                                                <th class="text-center">Min Alert</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->warehouseStockLimits->whereNull('product_variant_id') as $limit)
                                                <tr>
                                                    <td>{{ $limit->warehouse->name }}</td>
                                                    <td class="text-center">{{ $limit->min_stock }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold">Description:</div>
                        <div class="col-sm-9 text-muted">{!! $product->description !!}</div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Product Variations</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Variant Name</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th class="text-center">Stock</th>
                                    <th>Stock Limit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->variants as $variant)
                                <tr>
                                    <td>{{ $variant->variant_name ?? '-' }}</td>
                                    <td><code>{{ $variant->sku }}</code></td>
                                    <td>
                                        @if($variant->discount_price > 0)
                                            <span class="text-decoration-line-through text-muted small">{{ $gs->currency ?? '$' }}{{ number_format($variant->regular_price, 2) }}</span>
                                            <span class="text-danger fw-bold ms-1">{{ $gs->currency ?? '$' }}{{ number_format($variant->discount_price, 2) }}</span>
                                        @else
                                            {{ $gs->currency ?? '$' }}{{ number_format($variant->regular_price ?? $product->regular_price, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-center"><span class="badge badge-soft-dark">{{ $variant->stock ?? 0 }}</span></td>
                                    <td>
                                        @if($variant->min_stock_global > 0)
                                            <div class="mb-1">
                                                <span class="badge badge-soft-info">Global: {{ $variant->min_stock_global }}</span>
                                            </div>
                                        @endif

                                        @if($variant->warehouseStockLimits->count() > 0)
                                            <div class="mt-1 d-flex flex-wrap gap-1">
                                                @foreach($variant->warehouseStockLimits as $vLimit)
                                                    <div class="extra-small text-muted text-nowrap border rounded px-1">{{ $vLimit->warehouse->name }}: <strong>{{ $vLimit->min_stock }}</strong></div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($variant->min_stock_global <= 0 && $variant->warehouseStockLimits->count() == 0)
                                            <span class="text-muted small italic">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No specific variants created. Uses base pricing.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.thumbnail-image').on('click', function() {
            const newImage = $(this).data('full-image');
            
            // Update main image
            $('#main-product-image').attr('src', newImage);
            
            // Update border classes
            $('.thumbnail-image').removeClass('border-primary border-2');
            $(this).addClass('border-primary border-2');
        });
    });
</script>
@endsection
