@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Damaged Products (Wastage)</h4>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label">Search</label>
                    <div class="search-box">
                        <input type="text" class="form-control" id="search-input" placeholder="Search by Product Name or Reason..." value="{{ request('search') }}">
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
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th>Reason</th>
                            <th>Return ID</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wastages as $wastage)
                            <tr>
                                <td class="ps-3">{{ \App\HelperClass::indexNumberSerialization($wastages)[$loop->index] }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $wastage->product->primaryImage ? asset('storage/'.$wastage->product->primaryImage->image_path) : asset('admin_assets/images/no-image.png') }}" class="rounded-pill" style="width: 35px; height: 35px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fs-13">{{ $wastage->product->name }}</h6>
                                            @if($wastage->productVariant)
                                                <small class="text-muted">{{ $wastage->productVariant->variant_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><span class="badge bg-danger text-white">{{ $wastage->quantity }}</span></td>
                                <td>{{ $wastage->reason }}</td>
                                <td>
                                    @if($wastage->returnRequest)
                                        <a href="{{ route('admin.returns.show_request', $wastage->return_id) }}" class="fw-bold">
                                            {{ $wastage->returnRequest->return_id }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $wastage->created_at->format('M d, Y') }}</td>
                            </tr>
@empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No wastage records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($wastages->hasPages())
                <div class="card-footer border-top-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-muted">
                            Showing {{ $wastages->firstItem() }} to {{ $wastages->lastItem() }} of {{ $wastages->total() }} Results
                        </div>
                        {{ $wastages->appends(request()->query())->links('pagination::bootstrap-5') }}
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
            window.location.href = "{{ route('admin.returns.wastages') }}";
        });
    });
</script>
@endsection
