<!-- ========== Topbar Start ========== -->
<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <!-- Menu Toggle Button -->
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- Menu Toggle Button -->
                <div class="topbar-item">
                    <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">Welcome!</h4>
                </div>
            </div>

            <div class="d-flex align-items-center gap-1">

                <!-- Theme Color (Light/Dark) -->
                <div class="topbar-item">
                    <button type="button" class="topbar-button" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- Notification -->
                <div class="dropdown topbar-item">
                    <button type="button" class="topbar-button position-relative" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                        @if($unreadCount > 0)
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">{{ $unreadCount }}<span class="visually-hidden">unread messages</span></span>
                        @endif
                    </button>
                    <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end" aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold"> Notifications</h6>
                                </div>
                                <div class="col-auto">
                                    <form action="{{ route('admin.notifications.mark_all_read') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-dark text-decoration-underline p-0 border-0 align-baseline">
                                            <small>Mark all as read</small>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 280px;">
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
                        </div>
                        <div class="text-center py-3">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-primary btn-sm">View All Notification <i class="bx bx-right-arrow-alt ms-1"></i></a>
                        </div>
                    </div>
                </div>

                <!-- User -->
                <div class="dropdown topbar-item">
                    <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="d-flex align-items-center">
                                             <img class="rounded-circle avatar-sm" src="{{ Auth::guard('admin')->user()->image ? asset('storage/' . Auth::guard('admin')->user()->image) : asset('admin_assets/images/users/avatar-1.jpg') }}" alt="avatar">
                                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome {{ucwords(Auth::guard('admin')->user()->name)}}</h6>
                        <a class="dropdown-item" href="{{ route('admin.edit', Auth::guard('admin')->id()) }}">
                            <i class="bx bx-user-circle text-muted fs-18 align-middle me-1"></i><span class="align-middle">Profile</span>
                        </a>

                        <form method="post" action="{{route('admin.logout')}}">
                            <input type="hidden" name="type" value="admin">
                            @csrf
                        <button class="dropdown-item text-danger" type="submit">
                            <i class="bx bx-log-out fs-18 align-middle me-1"></i><span class="align-middle">Logout</span>
                        </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- ========== Topbar End ========== -->
