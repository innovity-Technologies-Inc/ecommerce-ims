@extends('client.structure.app')
@section('content')
    <!-- login area start -->
    <div class="login-register-area mb-60px mt-53px">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-12 mx-auto">
                    <div class="login-register-wrapper">
                        <div class="login-register-tab-list nav">
                            <a class="active" data-bs-toggle="tab">
                                <h4>User Registration</h4>
                            </a>
                        </div>
                        <div class="tab-content">
                            <div id="lg2" class="tab-pane active">
                                <div class="login-form-container">
                                    <div class="login-register-form">
                                        <form action="{{route('register')}}" method="post">
                                            @csrf

                                            @error('name')
                                            <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            <input type="text" name="name" placeholder="Full Name" value="{{old('name')}}" required>

                                            @error('email')
                                            <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            <input name="email" placeholder="Email" type="email" value="{{old('email')}}" required>

                                            @error('mobile')
                                            <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            <input name="mobile" placeholder="Mobile Number" value="{{old('mobile')}}" type="text" required>

                                            @error('address')
                                            <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            <input name="address" placeholder="Address" value="{{old('address')}}" type="text" required>

                                            @error('city')
                                            <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            <input name="city" placeholder="City" value="{{old('city')}}" type="text" required>

                                            @error('state')
                                            <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            <input name="state" placeholder="State" value="{{old('state')}}" type="text" required>

                                            @error('country')
                                            <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            <input name="country" placeholder="Country" value="{{old('country')}}" type="text" required>

                                            @error('zip')
                                            <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            <input name="zip" placeholder="Zip Code" value="{{old('zip')}}" type="text" required>

                                            @error('password')
                                            <span class="small text-danger">{{$message}}</span>
                                            @enderror
                                            <input type="password" name="password" placeholder="Password" required>

                                            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

                                            <div class="button-box">
                                                <button type="submit"><span>Register</span></button>
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
    </div>
    <!-- login area end -->
@endsection
