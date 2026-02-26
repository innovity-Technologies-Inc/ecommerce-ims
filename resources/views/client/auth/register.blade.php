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
                                <h4>register</h4>
                            </a>
                        </div>
                        <div class="tab-content">
                            <div id="lg2" class="tab-pane active">
                                <div class="login-form-container">
                                    <div class="login-register-form">
                                        <form action="{{route('register', 'user')}}" method="post">
                                            @csrf
                                            <input type="text" name="name" placeholder="Full Name">
                                            <input type="password" name="password" placeholder="Password">
                                            <input name="email" placeholder="Email" type="email">
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
