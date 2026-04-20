@extends('client.structure.app')
@section('content')

    @php
        $activeTab = session('active_tab', 'profile');
        if ($errors->hasAny(['name', 'email', 'mobile'])) {
            $activeTab = 'profile';
        } elseif ($errors->hasAny(['current_password', 'password'])) {
            $activeTab = 'password';
        } elseif ($errors->hasAny(['address', 'city', 'state', 'country', 'zip'])) {
            $activeTab = 'address';
        }
        $user = Auth::guard('web')->user();
    @endphp

<style>
    .profile-page-wrapper {
        padding: 60px 0;
        background-color: #f8fafc;
    }
    .profile-sidebar {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 30px 20px;
        height: 100%;
    }
    .profile-user-info {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        background: #7AAACE;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 700;
        margin: 0 auto 15px;
        box-shadow: 0 4px 10px rgba(122, 170, 206, 0.3);
    }
    .nav-profile {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .nav-profile .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 10px;
        color: #64748b;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    .nav-profile .nav-link iconify-icon {
        font-size: 20px;
    }
    .nav-profile .nav-link:hover {
        background: #f1f5f9;
        color: #7AAACE;
    }
    .nav-profile .nav-link.active {
        background: rgba(122, 170, 206, 0.1);
        color: #7AAACE;
        border-color: rgba(122, 170, 206, 0.2);
    }
    .profile-content-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 35px;
        border: none;
    }
    .section-header {
        margin-bottom: 30px;
    }
    .section-header h4 {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 5px;
    }
    .section-header p {
        color: #64748b;
        font-size: 14px;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .form-control {
        border-radius: 10px;
        padding: 12px 16px;
        border: 2px solid #f1f5f9;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #7AAACE;
        box-shadow: 0 0 0 4px rgba(122, 170, 206, 0.1);
    }
    .btn-profile-submit {
        background: #7AAACE;
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-profile-submit:hover {
        background: #6b99ba;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(122, 170, 206, 0.3);
        color: #fff;
    }
</style>

<div class="profile-page-wrapper">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="profile-sidebar">
                    <div class="profile-user-info">
                        <div class="profile-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h5 class="mb-1 fw-bold">{{ $user->name }}</h5>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                    </div>

                    <div class="nav nav-profile" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link {{ $activeTab === 'profile' ? 'active' : '' }}" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                            <iconify-icon icon="solar:user-bold-duotone"></iconify-icon>
                            Profile Information
                        </button>
                        <button class="nav-link {{ $activeTab === 'password' ? 'active' : '' }}" id="v-pills-password-tab" data-bs-toggle="pill" data-bs-target="#v-pills-password" type="button" role="tab">
                            <iconify-icon icon="solar:key-bold-duotone"></iconify-icon>
                            Account Security
                        </button>
                        <button class="nav-link {{ $activeTab === 'address' ? 'active' : '' }}" id="v-pills-address-tab" data-bs-toggle="pill" data-bs-target="#v-pills-address" type="button" role="tab">
                            <iconify-icon icon="solar:map-point-bold-duotone"></iconify-icon>
                            Shipping Address
                        </button>
                        <a href="{{ route('user.orders') }}" class="nav-link">
                            <iconify-icon icon="solar:bag-bold-duotone"></iconify-icon>
                            My Orders
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-8">
                <div class="tab-content" id="v-pills-tabContent">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade {{ $activeTab === 'profile' ? 'show active' : '' }}" id="v-pills-profile" role="tabpanel">
                        <div class="card profile-content-card">
                            <div class="section-header">
                                <h4>Personal Details</h4>
                                <p>Update your account's profile information and email address.</p>
                            </div>
                            <form method="post" action="{{route('user.profile.update')}}">
                                @csrf
                                @method('put')
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name)}}">
                                        @error('name') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email)}}">
                                        @error('email') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mobile Number</label>
                                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile)}}">
                                        @error('mobile') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn-profile-submit">Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Password Tab -->
                    <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" id="v-pills-password" role="tabpanel">
                        <div class="card profile-content-card">
                            <div class="section-header">
                                <h4>Account Security</h4>
                                <p>Ensure your account is using a long, random password to stay secure.</p>
                            </div>
                            <form method="post" action="{{route('user.password.update')}}">
                                @csrf
                                @method('put')
                                <div class="row g-3">
                                    @if($user->password)
                                    <div class="col-12">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
                                        @error('current_password') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    @endif
                                    <div class="col-md-6">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                                        @error('password') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input toggle-password-visibility" type="checkbox" id="show-password-profile">
                                            <label class="form-check-label text-muted small" for="show-password-profile">
                                                Show Passwords
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn-profile-submit">
                                            {{ $user->password ? 'Update Password' : 'Set Password' }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Address Tab -->
                    <div class="tab-pane fade {{ $activeTab === 'address' ? 'show active' : '' }}" id="v-pills-address" role="tabpanel">
                        <div class="card profile-content-card">
                            <div class="section-header">
                                <h4>Shipping Address</h4>
                                <p>Manage your default shipping and billing address for faster checkout.</p>
                            </div>
                            <form method="post" action="{{route('user.address.update')}}">
                                @csrf
                                @method('put')
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Street Address</label>
                                        <input type="text" name="address" class="form-control" value="{{ old('address', $user->address)}}" placeholder="123 Street Name">
                                        @error('address') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" class="form-control" value="{{ old('city', $user->city)}}" placeholder="Enter city">
                                        @error('city') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">State / Province</label>
                                        <input type="text" name="state" class="form-control" value="{{ old('state', $user->state)}}" placeholder="Enter state">
                                        @error('state') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Country</label>
                                        <input type="text" name="country" class="form-control" value="{{ old('country', $user->country)}}" placeholder="Enter country">
                                        @error('country') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Zip / Postal Code</label>
                                        <input type="text" name="zip" class="form-control" value="{{ old('zip', $user->zip)}}" placeholder="Enter zip code">
                                        @error('zip') <span class="text-danger small">{{$message}}</span> @enderror
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn-profile-submit">Save Address</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
