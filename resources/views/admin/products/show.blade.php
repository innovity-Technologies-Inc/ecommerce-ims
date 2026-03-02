@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Product Details</h4>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
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
                <div class="card-header">
                    <h4 class="card-title">{{ $product->name }}</h4>
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
                        <div class="col-sm-3 fw-bold">Description:</div>
                        <div class="col-sm-9 text-muted">{!! $product->description !!}</div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Product Variations</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                <tr>
                                    <td>{{ $variant->size ?? '-' }}</td>
                                    <td>{{ $variant->color ?? '-' }}</td>
                                    <td>{{ $variant->sku }}</td>
                                    <td>${{ number_format($variant->price, 2) }}</td>
                                </tr>
                                @endforeach
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
