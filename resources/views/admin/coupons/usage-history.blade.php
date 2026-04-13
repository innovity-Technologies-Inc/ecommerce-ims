@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Usage History for Coupon: <span class="text-primary">{{ $coupon->code }}</span></h4>
            <p class="text-muted mb-0 small">Overview of customers who used this coupon and the discounts applied.</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back to Coupons
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="p-3 border rounded bg-light-subtle">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Coupon Type</span>
                                <span class="fw-bold fs-16">{{ ucfirst($coupon->discount_type) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded bg-light-subtle">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Discount Amount</span>
                                <span class="fw-bold fs-16">
                                    {{ $coupon->discount_type == 'percentage' ? number_format($coupon->discount_amount, 0).'%' : '$'.number_format($coupon->discount_amount, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded bg-light-subtle">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Total Used</span>
                                <span class="fw-bold fs-16 text-primary">{{ $coupon->used_count }} Times</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded bg-light-subtle">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Usage Limit</span>
                                <span class="fw-bold fs-16">{{ $coupon->usage_limit ?? 'Unlimited' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Order ID</th>
                                    <th>Discount Applied</th>
                                    <th>Date Used</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sl = \App\HelperClass::indexNumberSerialization($usages);
                                @endphp
                                @forelse($usages as $usage)
                                    <tr>
                                        <td>{{ $sl++ }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $usage->user_name }}</span>
                                                <small class="text-muted">{{ $usage->user_email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $usage->order_id) }}" class="fw-bold text-primary">
                                                {{ $usage->order ? $usage->order->order_id : 'Order Removed' }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="text-danger fw-bold">${{ number_format($usage->discount_amount, 2) }}</span>
                                        </td>
                                        <td>
                                            {{ $usage->created_at->format('d M, Y - h:i A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted italic">This coupon has not been used yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer border-top">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-0 text-muted">
                                Showing {{ $usages->firstItem() ?? 0 }} to {{ $usages->lastItem() ?? 0 }} of {{ $usages->total() }} usages
                            </p>
                        </div>
                        <div>
                            {{ $usages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
