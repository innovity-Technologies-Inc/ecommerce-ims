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
                        <a href="{{ route('admin.notifications.read', $notification->id) }}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="View Details">
                            <i class="bx bx-show fs-16"></i>
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
<div class="card-footer border-top">
    {{ $notifications->appends(request()->all())->links() }}
</div>
