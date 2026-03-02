@extends('client.structure.app')
@section('content')

    <!-- account area start -->
    <div class="checkout-area mtb-60px">
        <div class="container">
            <div class="row">
                <div class="mx-auto col-lg-9">
                    <div class="checkout-wrapper">
                        <div id="faq" class="panel-group">
                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title">
                                    <h3 class="panel-title"><span>1 .</span> <a data-bs-toggle="collapse"
                                                                                data-parent="#faq" href="#my-account-1">Edit
                                            your account information </a></h3>
                                </div>
                                <div id="my-account-1" class="panel-collapse collapse show">
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
                                                                   value="{{ Auth::guard('web')->user()->name}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Email Address</label>
                                                            <input type="email" name="email"
                                                                   value="{{ Auth::guard('web')->user()->email}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Mobile</label>
                                                            <input type="text" name="mobile"
                                                                   value="{{Auth::guard('web')->user()->mobile}}">
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
                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title">
                                    <h3 class="panel-title"><span>2 .</span> <a data-bs-toggle="collapse"
                                                                                data-parent="#faq" href="#my-account-2">Change
                                            your password </a></h3>
                                </div>
                                <div id="my-account-2" class="panel-collapse collapse">
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
                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title">
                                    <h3 class="panel-title"><span>3 .</span> <a data-bs-toggle="collapse"
                                                                                data-parent="#faq" href="#my-account-3">Modify
                                            your address</a></h3>
                                </div>
                                <div id="my-account-3" class="panel-collapse collapse">
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
                                                                   value="{{ Auth::guard('web')->user()->address}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>City</label>
                                                            <input type="text" name="city"
                                                                   value="{{ Auth::guard('web')->user()->city}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>State</label>
                                                            <input type="text" name="state"
                                                                   value="{{Auth::guard('web')->user()->state}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Country</label>
                                                            <input type="text" name="country"
                                                                   value="{{Auth::guard('web')->user()->country}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="billing-info">
                                                            <label>Zip</label>
                                                            <input type="text" name="zip"
                                                                   value="{{Auth::guard('web')->user()->zip}}">
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
