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

            <div class="d-flex align-items-center gap-2">
                <div class="topbar-item d-none d-md-flex me-1">
                    <div class="clock-display">
                        <iconify-icon icon="solar:clock-circle-bold-duotone" class="fs-18 clock-icon"></iconify-icon>
                        <span id="digital-clock" class="fs-13">--:--:-- --</span>
                    </div>
                </div>

                <div class="topbar-item">
                    <form action="{{ route('admin.hrm.attendance.toggle') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn {{ auth('admin')->user()->is_clocked_in ? 'btn-soft-danger' : 'btn-soft-success' }} btn-sm d-flex align-items-center gap-1">
                            <iconify-icon icon="{{ auth('admin')->user()->is_clocked_in ? 'solar:stopwatch-play-bold-duotone' : 'solar:stopwatch-bold-duotone' }}"></iconify-icon>
                            {{ auth('admin')->user()->is_clocked_in ? 'Clock Out' : 'Clock In' }}
                        </button>
                    </form>
                </div>

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
                        <span id="ajax-unread-count" class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill {{ $unreadCount > 0 ? '' : 'd-none' }}">{{ $unreadCount }}<span class="visually-hidden">unread messages</span></span>
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
                        <div data-simplebar style="max-height: 280px;" id="notification-items-wrapper">
                            @include('admin.structure.partials.notification_items', ['unreadNotifications' => $unreadNotifications])
                        </div>
                        <div class="text-center py-3">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-primary btn-sm">View All Notification <i class="bx bx-right-arrow-alt ms-1"></i></a>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Digital Clock Logic
                        function updateClock() {
                            const now = new Date();
                            const timezone = "{{ config('app.timezone', 'UTC') }}";
                            
                            // Update Time
                            const timeString = now.toLocaleTimeString('en-US', { 
                                hour: '2-digit', 
                                minute: '2-digit', 
                                second: '2-digit', 
                                hour12: true,
                                timeZone: timezone
                            });
                            const clockElement = document.getElementById('digital-clock');
                            if (clockElement) {
                                clockElement.textContent = timeString;
                            }
                        }
                        setInterval(updateClock, 1000);
                        updateClock();

                        // Notification Logic
                        function refreshNotifications() {
                            // Only poll if tab is visible to save resources
                            if (document.hidden) return;

                            fetch("{{ route('admin.notifications.fetch_dropdown') }}")
                                .then(response => response.json())
                                .then(data => {
                                    const countBadge = document.getElementById('ajax-unread-count');
                                    const wrapper = document.getElementById('notification-items-wrapper');
                                    
                                    if (data.count > 0) {
                                        countBadge.textContent = data.count;
                                        countBadge.classList.remove('d-none');
                                    } else {
                                        countBadge.classList.add('d-none');
                                    }
                                    
                                    wrapper.innerHTML = data.html;
                                })
                                .catch(error => console.error('Notification Poll Error:', error));
                        }

                        // Poll every 60 seconds
                        setInterval(refreshNotifications, 60000);
                    });
                </script>

                <!-- User -->
                <div class="dropdown topbar-item">
                    <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            @php($adminUser = Auth::guard('admin')->user())
                            @if($adminUser->avatar)
                                <img class="rounded-circle avatar-sm" src="{{ asset('storage/' . $adminUser->avatar) }}" alt="avatar">
                            @elseif($adminUser->image)
                                <img class="rounded-circle avatar-sm" src="{{ asset('storage/' . $adminUser->image) }}" alt="avatar">
                            @else
                                <img class="rounded-circle avatar-sm" src="{{ asset('admin_assets/images/users/avatar-1.jpg') }}" alt="avatar">
                            @endif
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome {{ucwords($adminUser->name)}}</h6>
                        <a class="dropdown-item" href="{{ route('admin.profile.show') }}">
                            <i class="bx bx-user-circle text-muted fs-18 align-middle me-1"></i><span class="align-middle">My Profile</span>
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
