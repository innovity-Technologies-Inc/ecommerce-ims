<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th>Code</th>
                <th>Apply For</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Usage</th>
                <th>Active Range</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($coupons as $coupon)
                <tr>
                    <td>
                        <span class="fw-bold text-primary">{{ $coupon->code }}</span>
                    </td>
                    <td>
                        {{ str_replace('_', ' ', ucfirst($coupon->apply_for)) }}
                    </td>
                    <td>
                        <span class="badge {{ $coupon->discount_type == 'percentage' ? 'bg-info-subtle text-info' : 'bg-success-subtle text-success' }}">
                            {{ ucfirst($coupon->discount_type) }}
                        </span>
                    </td>
                    <td>
                        @if($coupon->discount_type == 'percentage')
                            {{ number_format($coupon->discount_amount, 0) }}% 
                            <small class="text-muted">(Max: {{ number_format($coupon->max_discount_amount, 2) }})</small>
                        @else
                            {{ number_format($coupon->discount_amount, 2) }}
                        @endif
                    </td>
                    <td>
                        {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
                    </td>
                    <td>
                        <div class="small">
                            <span class="text-success">From:</span> {{ $coupon->active_on->format('d M, Y') }}<br>
                            <span class="text-danger">To:</span> {{ $coupon->expired_on->format('d M, Y') }}
                        </div>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input status-toggle" type="checkbox" role="switch" 
                                data-id="{{ $coupon->id }}" {{ $coupon->status ? 'checked' : '' }} {{ auth('admin')->user()->can('coupons.edit') ? '' : 'disabled' }}>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.coupons.history', $coupon->id) }}" class="btn btn-soft-info btn-sm" title="Usage History">
                                <iconify-icon icon="solar:history-bold-duotone" class="fs-16"></iconify-icon>
                            </a>
                            @can('coupons.edit')
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-soft-primary btn-sm">
                                <iconify-icon icon="solar:pen-bold-duotone" class="fs-16"></iconify-icon>
                            </a>
                            @endcan
                            @can('coupons.delete')
                            <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete">
                                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="fs-16"></iconify-icon>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">No coupons found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <p class="mb-0 text-muted">
                Showing {{ $coupons->firstItem() ?? 0 }} to {{ $coupons->lastItem() ?? 0 }} of {{ $coupons->total() }} results
            </p>
        </div>
        <div class="pagination-container">
            {{ $coupons->appends(request()->query())->links() }}
        </div>
    </div>
</div>
