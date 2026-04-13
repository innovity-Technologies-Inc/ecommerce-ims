@extends('client.structure.app')
@section('content')

<div class="privacy-policy-area mt-60px mb-60px">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 p-4 p-md-5">
                    <div class="section-title mb-4">
                        <h2 class="title">{{ $title }}</h2>
                    </div>
                    <div class="policy-content fs-16 lh-lg text-dark">
                        {!! $policy->privacy_policy ?? '<p>Privacy Policy coming soon...</p>' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
