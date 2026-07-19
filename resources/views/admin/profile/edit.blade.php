@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Edit Profile</h4>
        <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Avatar Upload -->
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Profile Avatar</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if($admin->avatar)
                                    <img src="{{ \App\HelperClass::file_url($admin->avatar) }}" alt="{{ $admin->name }}" class="rounded-circle img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                @elseif($admin->image)
                                    <img src="{{ \App\HelperClass::file_url($admin->image) }}" alt="{{ $admin->name }}" class="rounded-circle img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="avatar-lg">
                                        <span class="avatar-title bg-soft-primary text-primary fs-20 rounded-circle">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <input class="form-control" type="file" id="avatar" name="avatar">
                            </div>
                            @error('avatar')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Recommended: Square image, max 2MB.</small>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <hr class="my-3">
                        <h5 class="mb-3">Change Password <small class="text-muted fs-12">(Leave blank to keep current)</small></h5>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
