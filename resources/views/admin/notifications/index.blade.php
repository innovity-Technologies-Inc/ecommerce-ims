@extends('admin.structure.app')

@section('title', 'All Notifications')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Notifications</h4>
                <div class="page-title-right">
                    <form action="{{ route('admin.notifications.mark_all_read') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-check-double me-1"></i> Mark All as Read
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.notifications.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Title or message..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>Order</option>
                                    <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                                    <option value="low_stock" {{ request('type') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="message" {{ request('type') == 'message' ? 'selected' : '' }}>Message</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-soft-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sl = \App\HelperClass::indexNumberSerialization($notifications); @endphp
                                @forelse($notifications as $notification)
                                    <tr class="{{ !$notification->is_read ? 'table-info' : '' }}">
                                        <td>{{ $sl++ }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $notification->title }}</span>
                                            @if(!$notification->is_read)
                                                <span class="badge bg-danger ms-1">New</span>
                                            @endif
                                        </td>
                                        <td class="text-wrap" style="max-width: 400px;">{{ $notification->message }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ match($notification->type) {
                                                'order' => 'bg-soft-primary text-primary',
                                                'return' => 'bg-soft-info text-info',
                                                'low_stock' => 'bg-soft-danger text-danger',
                                                'message' => 'bg-soft-warning text-warning',
                                                default => 'bg-soft-secondary text-secondary'
                                            } }} fs-12">
                                                {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ $notification->created_at->format('d M, Y') }}<br>
                                            <small class="text-muted">{{ $notification->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.notifications.read', $notification->id) }}" class="btn btn-soft-primary btn-sm">
                                                <i class="bx bx-show"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-48 mb-3 opacity-25"></iconify-icon>
                                                <p>No notifications found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer border-top">
                    {{ $notifications->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
