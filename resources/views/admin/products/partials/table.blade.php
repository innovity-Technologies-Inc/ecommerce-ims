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
                        <img src="{{ \App\HelperClass::file_url($data->primaryImage->image_path) }}" alt="{{ $data->name }}" class="avatar-sm rounded">
                    @else
                        <span class="text-muted">No Image</span>
                    @endif
                </td>
                <td>{{$data->name}}</td>
                <td>
                    {{ $data->category ? $data->category->name : '-' }}
                    @if($data->subCategory)
                        <br>
                        <small class="text-muted"> <i class="bx bx-subdirectory-right"></i> {{ $data->subCategory->name }}</small>
                    @endif
                </td>
                <td>
                    @php
                        $priceData = \App\HelperClass::getProductPriceRange($data);
                        $gs = \App\HelperClass::generalSettings();
                    @endphp
                    
                    {{ $gs->currency ?? '$' }}{{ number_format($priceData['min'], 2) }} 
                    @if($priceData['has_range'])
                        - {{ $gs->currency ?? '$' }}{{ number_format($priceData['max'], 2) }}
                    @endif
                </td>
                <td>
                    <form action="{{ route('admin.products.toggle-status', $data->id) }}" method="POST">
                        @csrf
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="status_{{ $data->id }}" onChange="this.form.submit()" {{ $data->status ? 'checked' : '' }} {{ auth('admin')->user()->can('products.edit') ? '' : 'disabled' }}>
                        </div>
                    </form>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.show', $data->id) }}" class="btn btn-soft-info btn-sm" data-bs-toggle="tooltip" title="View Details">
                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                        </a>
                        @can('products.edit')
                        <a href="{{ route('admin.products.edit', $data->id) }}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="Edit Product">
                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                        </a>
                        @endcan
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
    {{ $products->appends(request()->all())->links() }}
</div>
