@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">My Profile</h4>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                    <div class="position-relative mb-4">
                        @php($adminAvatar = $admin->avatar ?? $admin->image)
                        @if($adminAvatar)
                            <img src="{{ asset('storage/' . $adminAvatar) }}" alt="{{ $admin->name }}" class="rounded-circle img-thumbnail shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="mx-auto" style="width: 150px; height: 150px;">
                                <span class="avatar-title bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center h-100 w-100 fw-bold" style="font-size: 64px;">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <button type="button" class="btn btn-primary rounded-circle position-absolute d-flex align-items-center justify-content-center shadow" 
                                style="width: 42px; height: 42px; bottom: 5px; right: 5px;"
                                data-bs-toggle="modal" data-bs-target="#changeAvatarModal" title="Change Avatar">
                            <iconify-icon icon="solar:camera-minimalistic-bold-duotone" class="fs-22"></iconify-icon>
                        </button>
                    </div>
                    <h4 class="mb-1 text-dark fw-bold">{{ $admin->name }}</h4>
                    <p class="text-muted fs-15 mb-3">{{ $admin->getRoleNames()->first() ?? 'No Role' }}</p>
                    
                    <div class="d-grid gap-2 w-100">
                        <button type="button" class="btn btn-outline-primary btn-sm py-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <iconify-icon icon="solar:key-bold-duotone" class="align-middle me-1 fs-16"></iconify-icon> Change Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 fw-bold">Profile Information</h4>
                    <button type="button" class="btn btn-soft-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <iconify-icon icon="solar:pen-new-square-bold-duotone" class="align-middle me-1 fs-16"></iconify-icon> Edit Profile
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4 align-items-center">
                        <div class="col-sm-3">
                            <h6 class="mb-0 fw-semibold text-muted">Full Name</h6>
                        </div>
                        <div class="col-sm-9 text-dark fs-15 fw-medium">
                            {{ $admin->name }}
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <div class="col-sm-3">
                            <h6 class="mb-0 fw-semibold text-muted">Email Address</h6>
                        </div>
                        <div class="col-sm-9 text-dark fs-15 fw-medium">
                            {{ $admin->email }}
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <div class="col-sm-3">
                            <h6 class="mb-0 fw-semibold text-muted">Role</h6>
                        </div>
                        <div class="col-sm-9 text-dark fs-15">
                            <span class="badge bg-soft-info text-info px-3 py-2 fs-12">{{ $admin->getRoleNames()->first() ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="row mb-0 align-items-center">
                        <div class="col-sm-3">
                            <h6 class="mb-0 fw-semibold text-muted">Member Since</h6>
                        </div>
                        <div class="col-sm-9 text-dark fs-15 fw-medium">
                            {{ optional($admin->created_at)->format('d M, Y') ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Profile -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.profile.update_details') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Change Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.profile.update_password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Change Avatar -->
<div class="modal fade" id="changeAvatarModal" tabindex="-1" aria-labelledby="changeAvatarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeAvatarModalLabel">Change Profile Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.profile.update_avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-center">
                    <div class="mb-3">
                        @if($admin->avatar)
                            <img src="{{ asset('storage/' . $admin->avatar) }}" alt="{{ $admin->name }}" class="rounded-circle img-thumbnail mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        @elseif($admin->image)
                            <img src="{{ asset('storage/' . $admin->image) }}" alt="{{ $admin->name }}" class="rounded-circle img-thumbnail mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        @endif
                        <input class="form-control" type="file" id="avatar" name="avatar" required>
                        <div class="form-text mt-2">Recommended: Square image, max 2MB.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Image</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Auto-open modal if validation errors exist for specific forms
    $(document).ready(function() {
        @if ($errors->hasAny(['name', 'email']))
            new bootstrap.Modal(document.getElementById('editProfileModal')).show();
        @endif
        
        @if ($errors->has('password'))
            new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
        @endif
        
        @if ($errors->has('avatar'))
            new bootstrap.Modal(document.getElementById('changeAvatarModal')).show();
        @endif
    });
</script>
@endsection
