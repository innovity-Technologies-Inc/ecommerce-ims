@extends('client.structure.app')
@section('content')
    <section class="about-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-res-sm-50px mx-auto">
                    <div class="mb-4 text-muted">
                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 fw-medium text-success small">
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </div>
                    @endif

                    <div class="mt-4 d-flex align-items-center justify-content-between">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf

                            <div>
                                <button type="submit" class="btn btn-secondary">
                                    {{ __('Resend Verification Email') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
