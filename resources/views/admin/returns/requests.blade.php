@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Return Requests</h4>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">Search</label>
                    <div class="search-box">
                        <input type="text" class="form-control" id="search-input" placeholder="Search by Return ID or Order ID..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">Status</label>
                    <select class="form-select filter-select" id="status-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                    </select>
                </div>
                <div class="col-lg-4 d-flex align-items-end">
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
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sl = \App\HelperClass::indexNumberSerialization($requests); @endphp
                        @forelse($requests as $request)
                            <tr>
                                <td class="ps-3">{{ $sl++ }}</td>
                                <td><span class="fw-bold">{{ $request->return_id }}</span></td>
                                <td>{{ $request->order->order_id }}</td>
                                <td>
                                    @if($request->user)
                                        {{ $request->user->name }}
                                    @else
                                        <span class="text-muted">Guest</span>
                                    @endif
                                </td>
                                <td><span class="text-truncate d-inline-block" style="max-width: 150px;">{{ $request->reason }}</span></td>
                                <td>
                                    <span class="badge {{ match($request->status) {
                                        'pending' => 'bg-warning-subtle text-warning',
                                        'approved' => 'bg-info-subtle text-info',
                                        'received' => 'bg-success-subtle text-success',
                                        'rejected' => 'bg-danger-subtle text-danger',
                                        default => 'bg-secondary-subtle text-secondary'
                                    } }} px-2 py-1">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.returns.show_request', $request->id) }}" class="btn btn-soft-primary btn-sm">
                                        <i class="bx bx-show"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No return requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
                <div class="card-footer border-top-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-muted">
                            Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} Results
                        </div>
                        {{ $requests->appends(request()->query())->links('pagination::bootstrap-5') }}
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
            const status = $('#status-select').val();

            const url = new URL(window.location.href);
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (status) url.searchParams.set('status', status); else url.searchParams.delete('status');
            
            window.location.href = url.toString();
        }

        $('#search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 1000);
        });

        $('.filter-select').on('change', applyFilters);

        $('#reset-filters').on('click', function() {
            window.location.href = "{{ route('admin.returns.requests') }}";
        });
    });
</script>
@endsection
