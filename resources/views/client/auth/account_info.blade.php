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
    .account-page-wrapper {
        padding: 80px 0;
        background-color: #f9fafb;
    }
    
    /* Sidebar Styling */
    .account-sidebar {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        padding: 40px 25px;
        border: 1px solid #f1f5f9;
    }
    
    .account-user-card {
        text-align: center;
        margin-bottom: 35px;
        padding-bottom: 25px;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .account-avatar {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #7AAACE 0%, #5a8fb2 100%);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 700;
        margin: 0 auto 15px;
        box-shadow: 0 8px 20px rgba(122, 170, 206, 0.25);
        position: relative;
        overflow: hidden;
    }

    .account-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-edit-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 32px;
        height: 32px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        z-index: 3;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }

    .avatar-edit-btn:hover {
        background: #7AAACE;
        color: #fff;
        transform: scale(1.1);
    }

    .avatar-container {
        position: relative;
        width: 110px;
        margin: 0 auto 15px;
    }
    
    .account-nav {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .account-nav .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        border-radius: 12px;
        color: #64748b;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        text-align: left;
    }
    
    .account-nav .nav-link iconify-icon {
        font-size: 20px;
        transition: transform 0.3s ease;
    }
    
    .account-nav .nav-link:hover {
        background: #f8fafc;
        color: #7AAACE;
    }
    
    .account-nav .nav-link:hover iconify-icon {
        transform: translateX(3px);
    }
    
    .account-nav .nav-link.active {
        background: #7AAACE;
        color: #fff;
        box-shadow: 0 4px 12px rgba(122, 170, 206, 0.2);
    }
    
    /* Content Card Styling */
    .account-content-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        padding: 40px;
        border: 1px solid #f1f5f9;
        height: 100%;
    }
    
    .account-section-title {
        margin-bottom: 35px;
        position: relative;
    }
    
    .account-section-title h3 {
        font-weight: 800;
        color: #1e293b;
        font-size: 24px;
        margin-bottom: 8px;
    }
    
    .account-section-title p {
        color: #94a3b8;
        font-size: 15px;
        margin-bottom: 0;
    }
    
    /* Form Elements */
    .form-group-custom {
        margin-bottom: 25px;
    }
    
    .form-label-custom {
        font-weight: 700;
        color: #334155;
        margin-bottom: 10px;
        font-size: 14px;
        display: block;
    }
    
    .form-control-custom {
        border-radius: 12px;
        padding: 14px 18px;
        border: 2px solid #f1f5f9;
        background-color: #f8fafc;
        color: #1e293b;
        font-weight: 500;
        font-size: 15px;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .form-control-custom:focus {
        border-color: #7AAACE;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(122, 170, 206, 0.1);
        outline: none;
    }
    
    .btn-save-custom {
        background: #7AAACE;
        color: #fff;
        border: none;
        padding: 16px 40px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.3s ease;
        width: auto;
    }
    
    .btn-save-custom:hover {
        background: #6b99ba;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 170, 206, 0.3);
        color: #fff;
    }
    
    .btn-save-custom:active {
        transform: translateY(0);
    }
    
    /* Error styling */
    .error-msg {
        color: #ef4444;
        font-size: 13px;
        font-weight: 600;
        margin-top: 6px;
        display: block;
    }
    
    @media (max-width: 991px) {
        .account-page-wrapper {
            padding: 40px 0;
        }
        .account-sidebar {
            margin-bottom: 30px;
        }
        .account-content-card {
            padding: 30px 20px;
        }
    }
</style>

<div class="account-page-wrapper">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-4">
                <div class="account-sidebar">
                    <div class="account-user-card">
                        <div class="avatar-container">
                            <div class="account-avatar">
                                @if($user->image)
                                    <img src="{{ \App\HelperClass::file_url($user->image) }}" alt="{{ $user->name }}">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="avatar-edit-btn" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                                <iconify-icon icon="solar:camera-bold-duotone"></iconify-icon>
                            </div>
                        </div>
                        <h4 class="mb-1 fw-bold text-dark">{{ $user->name }}</h4>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                    </div>

                    <div class="nav account-nav" id="accountTabs" role="tablist">
                        <button class="nav-link {{ $activeTab === 'profile' ? 'active' : '' }}" 
                                id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" 
                                type="button" role="tab">
                            <iconify-icon icon="solar:user-bold-duotone"></iconify-icon>
                            Profile Information
                        </button>
                        
                        <button class="nav-link {{ $activeTab === 'password' ? 'active' : '' }}" 
                                id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" 
                                type="button" role="tab">
                            <iconify-icon icon="solar:shield-keyhole-bold-duotone"></iconify-icon>
                            Account Security
                        </button>
                        
                        <button class="nav-link {{ $activeTab === 'address' ? 'active' : '' }}" 
                                id="address-tab" data-bs-toggle="tab" data-bs-target="#address-pane" 
                                type="button" role="tab">
                            <iconify-icon icon="solar:map-point-bold-duotone"></iconify-icon>
                            Shipping Address
                        </button>
                        
                        <a href="{{ route('user.orders') }}" class="nav-link">
                            <iconify-icon icon="solar:bag-heart-bold-duotone"></iconify-icon>
                            Order History
                        </a>
                        
                        <form action="{{ route('logout') }}" method="post" class="mt-2">
                            @csrf
                            <input type="hidden" name="type" value="user">
                            <button type="submit" class="nav-link w-100 text-danger" style="background: transparent;">
                                <iconify-icon icon="solar:logout-bold-duotone"></iconify-icon>
                                Logout Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-8">
                <div class="tab-content" id="accountTabsContent">
                    
                    <!-- Profile Information Pane -->
                    <div class="tab-pane fade {{ $activeTab === 'profile' ? 'show active' : '' }}" 
                         id="profile-pane" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="account-content-card">
                            <div class="account-section-title">
                                <h3>Personal Details</h3>
                                <p>View and update your basic account information.</p>
                            </div>
                            
                            <form method="post" action="{{ route('user.profile.update') }}">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-12 form-group-custom">
                                        <label class="form-label-custom">Full Name</label>
                                        <input type="text" name="name" class="form-control-custom" 
                                               value="{{ old('name', $user->name) }}" placeholder="Your Name">
                                        @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-md-6 form-group-custom">
                                        <label class="form-label-custom">Email Address</label>
                                        <input type="email" name="email" class="form-control-custom" 
                                               value="{{ old('email', $user->email) }}" placeholder="email@example.com">
                                        @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-md-6 form-group-custom">
                                        <label class="form-label-custom">Mobile Number</label>
                                        <input type="text" name="mobile" class="form-control-custom" 
                                               value="{{ old('mobile', $user->mobile) }}" placeholder="Phone Number">
                                        @error('mobile') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-12 pt-3">
                                        <button type="submit" class="btn-save-custom">Update Profile</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Security Pane -->
                    <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" 
                         id="security-pane" role="tabpanel" aria-labelledby="security-tab">
                        <div class="account-content-card">
                            <div class="account-section-title">
                                <h3>Account Security</h3>
                                <p>Manage your password and keep your account safe.</p>
                            </div>
                            
                            <form method="post" action="{{ route('user.password.update') }}">
                                @csrf
                                @method('put')
                                <div class="row">
                                    @if($user->password)
                                    <div class="col-12 form-group-custom">
                                        <label class="form-label-custom">Current Password</label>
                                        <input type="password" name="current_password" class="form-control-custom" 
                                               placeholder="Enter your current password">
                                        @error('current_password') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    @endif
                                    
                                    <div class="col-md-6 form-group-custom">
                                        <label class="form-label-custom">New Password</label>
                                        <input type="password" name="password" class="form-control-custom" 
                                               placeholder="••••••••">
                                        @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-md-6 form-group-custom">
                                        <label class="form-label-custom">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="form-control-custom" 
                                               placeholder="••••••••">
                                    </div>
                                    
                                    <div class="col-12 mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input toggle-password-visibility" type="checkbox" id="show-pass">
                                            <label class="form-check-label text-muted small fw-bold" for="show-pass">
                                                Show Passwords
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 pt-2">
                                        <button type="submit" class="btn-save-custom">
                                            {{ $user->password ? 'Update Password' : 'Set Password' }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Shipping Address Pane -->
                    <div class="tab-pane fade {{ $activeTab === 'address' ? 'show active' : '' }}" 
                         id="address-pane" role="tabpanel" aria-labelledby="address-tab">
                        <div class="account-content-card">
                            <div class="account-section-title">
                                <h3>Shipping Address</h3>
                                <p>Set your default delivery address for a faster checkout.</p>
                            </div>
                            
                            <form method="post" action="{{ route('user.address.update') }}">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-12 form-group-custom">
                                        <label class="form-label-custom">Street Address</label>
                                        <input type="text" name="address" class="form-control-custom" 
                                               value="{{ old('address', $user->address) }}" placeholder="123 Shopping St.">
                                        @error('address') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-md-6 form-group-custom">
                                        <label class="form-label-custom">City</label>
                                        <input type="text" name="city" class="form-control-custom" 
                                               value="{{ old('city', $user->city) }}" placeholder="City">
                                        @error('city') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-md-6 form-group-custom">
                                        <label class="form-label-custom">State / Province</label>
                                        <input type="text" name="state" class="form-control-custom" 
                                               value="{{ old('state', $user->state) }}" placeholder="State">
                                        @error('state') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-md-6 form-group-custom">
                                        <label class="form-label-custom">Country</label>
                                        <input type="text" name="country" class="form-control-custom" 
                                               value="{{ old('country', $user->country) }}" placeholder="Country">
                                        @error('country') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-md-6 form-group-custom">
                                        <label class="form-label-custom">Zip / Postal Code</label>
                                        <input type="text" name="zip" class="form-control-custom" 
                                               value="{{ old('zip', $user->zip) }}" placeholder="Zip Code">
                                        @error('zip') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-12 pt-3">
                                        <button type="submit" class="btn-save-custom">Save Address</button>
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

<!-- Change Avatar Modal -->
<div class="modal fade" id="changeAvatarModal" tabindex="-1" aria-labelledby="changeAvatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="changeAvatarModalLabel">Update Profile Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.profile.image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="form-group-custom mb-0">
                        <label class="form-label-custom">Select New Image</label>
                        <input type="file" name="image" class="form-control-custom" accept="image/*" required>
                        <p class="text-muted small mt-2 mb-0">Recommended: Square image, max 2MB (JPG, PNG, WebP, AVIF).</p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 12px; padding: 12px 25px;">Cancel</button>
                    <button type="submit" class="btn-save-custom">Upload Image</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
