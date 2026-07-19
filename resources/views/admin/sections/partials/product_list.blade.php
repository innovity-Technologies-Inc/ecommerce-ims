@foreach($products as $product)
<div class="d-flex align-items-center justify-content-between p-2 border-bottom">
    <div class="d-flex align-items-center">
        @php
            $imagePath = $product->primaryImage ? $product->primaryImage->image_path : 'admin_assets/assets/images/logo-sm.png';
            $imageUrl = $product->primaryImage ? \App\HelperClass::file_url($imagePath) : asset($imagePath);
            $priceData = \App\HelperClass::getProductPriceRange($product);
            $gs = \App\HelperClass::generalSettings();
        @endphp
        <img src="{{ $imageUrl }}" alt="" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
        <div>
            <h6 class="mb-0 text-truncate" style="max-width: 200px;">{{ $product->name }}</h6>
            <small class="text-muted">
                {{ $gs->currency ?? '$' }}{{ number_format($priceData['min'], 2) }}
                @if($priceData['has_range'])
                    - {{ $gs->currency ?? '$' }}{{ number_format($priceData['max'], 2) }}
                @endif
            </small>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-primary add-product-btn" 
        data-id="{{ $product->id }}" 
        data-name="{{ $product->name }}"
        data-price="{{ $priceData['min'] }}"
        data-image="{{ $imageUrl }}">
        Add
    </button>
</div>
@endforeach

@if($products->isEmpty())
<div class="text-center py-4 text-muted">
    No products found.
</div>
@endif

<div class="mt-3">
    {{ $products->appends(request()->all())->links() }}
</div>
