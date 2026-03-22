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
        $panelIndex = 1;
        $user = Auth::guard('web')->user();
    @endphp

    <!-- account area start -->
    <div class="checkout-area mtb-60px">
        <div class="container">
            <div class="row">
                <div class="mx-auto col-lg-9">
                    <div class="checkout-wrapper">
                        <div id="faq" class="panel-group">
                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title">
                                    <h3 class="panel-title"><span>{{ $panelIndex++ }} .</span> <a data-bs-toggle="collapse"
                                                                                data-parent="#faq" href="#my-account-1"
                                                                                class="{{ $activeTab !== 'profile' ? 'collapsed' : '' }}"
                                                                                aria-expanded="{{ $activeTab === 'profile' ? 'true' : 'false' }}">Edit
                                            your account information </a></h3>
                                </div>
                                <div id="my-account-1" class="panel-collapse collapse {{ $activeTab === 'profile' ? 'show' : '' }}">
                                    <form method="post" action="{{route('user.profile.update')}}">
                                        @csrf
                                        @method('put')
                                        <div class="panel-body">
                                            <div class="myaccount-info-wrapper">
                                                <div class="account-info-wrapper">
                                                    <h5>Your Personal Details</h5>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Full Name</label>
                                                            <input type="text" name="name"
                                                                   value="{{ old('name', Auth::guard('web')->user()->name)}}">
                                                            @error('name')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Email Address</label>
                                                            <input type="email" name="email"
                                                                   value="{{ old('email', Auth::guard('web')->user()->email)}}">
                                                            @error('email')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Mobile</label>
                                                            <input type="text" name="mobile"
                                                                   value="{{ old('mobile', Auth::guard('web')->user()->mobile)}}">
                                                            @error('mobile')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="billing-back-btn">
                                                    <div class="billing-back">
                                                        <a href="#"><i class="fa fa-arrow-up"></i> back</a>
                                                    </div>
                                                    <div class="billing-btn">
                                                        <button type="submit">Continue</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            @if(!$user->google_id)
                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title">
                                    <h3 class="panel-title"><span>{{ $panelIndex++ }} .</span> <a data-bs-toggle="collapse"
                                                                                data-parent="#faq" href="#my-account-2"
                                                                                class="{{ $activeTab !== 'password' ? 'collapsed' : '' }}"
                                                                                aria-expanded="{{ $activeTab === 'password' ? 'true' : 'false' }}">Change
                                            your password </a></h3>
                                </div>
                                <div id="my-account-2" class="panel-collapse collapse {{ $activeTab === 'password' ? 'show' : '' }}">
                                    <form method="post" action="{{route('user.password.update')}}">
                                        @csrf
                                        @method('put')
                                        <div class="panel-body">
                                            <div class="myaccount-info-wrapper">
                                                <div class="account-info-wrapper">
                                                    <h5>Change Password</h5>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">

                                                            <label>Current Password</label>
                                                            <input type="password" name="current_password">
                                                            @error('current_password')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Password</label>
                                                            <input type="password" name="password">
                                                            @error('password')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Password Confirm</label>
                                                            <input type="password" name="password_confirmation">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="billing-back-btn">
                                                    <div class="billing-back">
                                                        <a href="#"><i class="fa fa-arrow-up"></i> back</a>
                                                    </div>
                                                    <div class="billing-btn">
                                                        <button type="submit">Continue</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif

                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title">
                                    <h3 class="panel-title"><span>{{ $panelIndex++ }} .</span> <a data-bs-toggle="collapse"
                                                                                data-parent="#faq" href="#my-account-3"
                                                                                class="{{ $activeTab !== 'address' ? 'collapsed' : '' }}"
                                                                                aria-expanded="{{ $activeTab === 'address' ? 'true' : 'false' }}">Modify
                                            your address</a></h3>
                                </div>
                                <div id="my-account-3" class="panel-collapse collapse {{ $activeTab === 'address' ? 'show' : '' }}">
                                    <form method="post" action="{{route('user.address.update')}}">
                                        @csrf
                                        @method('put')
                                        <div class="panel-body">
                                            <div class="myaccount-info-wrapper">
                                                <div class="account-info-wrapper">
                                                    <h5>Address</h5>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Address</label>
                                                            <input type="text" name="address"
                                                                   value="{{ old('address', Auth::guard('web')->user()->address)}}">
                                                            @error('address')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>City</label>
                                                            <input type="text" name="city"
                                                                   value="{{ old('city', Auth::guard('web')->user()->city)}}">
                                                            @error('city')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>State</label>
                                                            <input type="text" name="state"
                                                                   value="{{ old('state', Auth::guard('web')->user()->state)}}">
                                                            @error('state')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Country</label>
                                                            <input type="text" name="country"
                                                                   value="{{ old('country', Auth::guard('web')->user()->country)}}">
                                                            @error('country')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Zip</label>
                                                            <input type="text" name="zip"
                                                                   value="{{ old('zip', Auth::guard('web')->user()->zip)}}">
                                                            @error('zip')
                                                            <span class="small text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="billing-back-btn">
                                                    <div class="billing-back">
                                                        <a href="#"><i class="fa fa-arrow-up"></i> back</a>
                                                    </div>
                                                    <div class="billing-btn">
                                                        <button type="submit">Continue</button>
                                                    </div>
                                                </div>
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
    </div>
    <!-- account area end -->

@endsection
