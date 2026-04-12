@forelse($unreadNotifications as $notif)
    <a href="{{ route('admin.notifications.read', $notif->id) }}" class="dropdown-item py-3 border-bottom text-wrap">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="avatar-sm me-2">
                    <span class="avatar-title rounded-circle fs-18 {{ match($notif->type) {
                        'order' => 'bg-soft-primary text-primary',
                        'return' => 'bg-soft-info text-info',
                        'low_stock' => 'bg-soft-danger text-danger',
                        'message' => 'bg-soft-warning text-warning',
                        default => 'bg-soft-secondary text-secondary'
                    } }}">
                        <iconify-icon icon="{{ match($notif->type) {
                            'order' => 'solar:cart-large-bold-duotone',
                            'return' => 'solar:restart-bold-duotone',
                            'low_stock' => 'solar:danger-bold-duotone',
                            'message' => 'solar:letter-bold-duotone',
                            default => 'solar:bell-bing-bold-duotone'
                        } }}"></iconify-icon>
                    </span>
                </div>
            </div>
            <div class="flex-grow-1">
                <p class="mb-0 fw-semibold">{{ $notif->title }}</p>
                <p class="mb-0 text-muted small">{{ Str::limit($notif->message, 60) }}</p>
                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </a>
@empty
    <div class="text-center py-4">
        <p class="text-muted mb-0">No new notifications</p>
    </div>
@endforelse
