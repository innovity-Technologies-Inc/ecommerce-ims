<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price Range</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @php
            $sl = \App\HelperClass::indexNumberSerialization($products);
        @endphp
        @foreach ($products as $data)
            <tr>
                <td>{{$sl++}}</td>
                <td>
                    @if($data->primaryImage)
                        <img src="{{ asset('storage/'.$data->primaryImage->image_path) }}" alt="{{ $data->name }}" class="avatar-sm rounded">
                    @else
                        <span class="text-muted">No Image</span>
                    @endif
                </td>
                <td>{{$data->name}}</td>
                <td>{{ $data->category ? $data->category->name : '-' }}</td>
                <td>
                    @php
                        $gs = \App\HelperClass::generalSettings();
                        $prices = collect();
                        
                        if($data->variants->count() > 0) {
                            foreach($data->variants as $variant) {
                                $prices->push($variant->discount_price ?? $variant->regular_price ?? $data->discount_price ?? $data->regular_price);
                            }
                        } else {
                            $prices->push($data->discount_price ?? $data->regular_price);
                        }
                        
                        $prices = $prices->filter();
                        $minPrice = $prices->min() ?? 0;
                        $maxPrice = $prices->max() ?? 0;
                    @endphp
                    
                    {{ $gs->currency ?? '$' }}{{ number_format($minPrice, 2) }} 
                    @if($minPrice != $maxPrice)
                        - {{ $gs->currency ?? '$' }}{{ number_format($maxPrice, 2) }}
                    @endif
                </td>
                <td>
                    <form action="{{ route('admin.products.toggle-status', $data->id) }}" method="POST">
                        @csrf
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="status_{{ $data->id }}" onChange="this.form.submit()" {{ $data->status ? 'checked' : '' }}>
                        </div>
                    </form>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.show', $data->id) }}" class="btn btn-soft-info btn-sm">
                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                        </a>
                        <a href="{{ route('admin.products.edit', $data->id) }}" class="btn btn-soft-primary btn-sm">
                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                        </a>
                        <form action="{{ route('admin.products.destroy', $data->id) }}" method="post" class="d-inline">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete">
                                <iconify-icon icon="solar:trash-bin-trash-broken" class="align-middle fs-18"></iconify-icon>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            @if($products->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">No products found.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted">
            Showing <span class="fw-semibold">{{ $products->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $products->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $products->total() }}</span> Results
        </div>
        <div>
            {{ $products->appends(request()->all())->links() }}
        </div>
    </div>
</div>
