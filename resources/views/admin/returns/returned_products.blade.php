@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Returned Products</h4>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label">Search</label>
                    <div class="search-box">
                        <input type="text" class="form-control" id="search-input" placeholder="Search by Return ID or Product Name..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100" id="reset-filters">Reset Filters</button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-nowrap mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Return ID</th>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Condition</th>
                            <th>Received Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sl = \App\HelperClass::indexNumberSerialization($items); @endphp
                        @forelse($items as $item)
                            <tr>
                                <td class="ps-3">{{ $sl++ }}</td>
                                <td><span class="fw-bold">{{ $item->returnRequest->return_id }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $item->product->primaryImage ? asset('storage/'.$item->product->primaryImage->image_path) : asset('admin_assets/images/no-image.png') }}" class="rounded-pill" style="width: 35px; height: 35px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fs-13">{{ $item->product->name }}</h6>
                                            @if($item->productVariant)
                                                <small class="text-muted">{{ $item->productVariant->variant_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $item->condition === 'intact' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ ucfirst($item->condition) }}
                                    </span>
                                </td>
                                <td>{{ $item->updated_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No returned products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($items->hasPages())
                <div class="card-footer border-top-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-muted">
                            Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} Results
                        </div>
                        {{ $items->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;

        function applyFilters() {
            const search = $('#search-input').val();
            const url = new URL(window.location.href);
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            window.location.href = url.toString();
        }

        $('#search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 1000);
        });

        $('#reset-filters').on('click', function() {
            window.location.href = "{{ route('admin.returns.returned_products') }}";
        });
    });
</script>
@endsection
