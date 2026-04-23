@extends('admin.structure.app')

@section('content')
@php $gs = \App\HelperClass::generalSettings(); @endphp
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.reports.customers.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0">Customer Analytics List</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm filter-card">
        <div class="card-body">
            <form action="{{ route('admin.reports.customers.list') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, Email, Mobile..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Sort By</label>
                    <select name="sort_by" class="form-select">
                        <option value="orders_sum_total_amount" {{ ($filters['sort_by'] ?? '') == 'orders_sum_total_amount' ? 'selected' : '' }}>Total Spent</option>
                        <option value="orders_count" {{ ($filters['sort_by'] ?? '') == 'orders_count' ? 'selected' : '' }}>Order Count</option>
                        <option value="last_order_date" {{ ($filters['sort_by'] ?? '') == 'last_order_date' ? 'selected' : '' }}>Last Order</option>
                        <option value="name" {{ ($filters['sort_by'] ?? '') == 'name' ? 'selected' : '' }}>Name</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Order</label>
                    <select name="sort_order" class="form-select">
                        <option value="desc" {{ ($filters['sort_order'] ?? '') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ ($filters['sort_order'] ?? '') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bx bx-search-alt me-1"></i> Search
                    </button>
                    <a href="{{ route('admin.reports.customers.list') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Customer Information</th>
                            <th class="text-center">Orders</th>
                            <th class="text-end">Total Spent</th>
                            <th class="text-center">Last Order</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-soft-secondary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                            <span class="fw-bold text-secondary">{{ substr($customer->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $customer->name }}</h6>
                                            <small class="text-muted">{{ $customer->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-primary text-primary px-3">{{ $customer->orders_count }}</span>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    {{ $gs->currency ?? '$' }}{{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}
                                </td>
                                <td class="text-center small">
                                    {{ $customer->last_order_date ? \Carbon\Carbon::parse($customer->last_order_date)->format('d M, Y') : 'N/A' }}
                                </td>
                                <td class="text-center">
                                    @if($customer->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="View Details">
                                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">No customers found matching the criteria.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} results
                </div>
                <div>
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
