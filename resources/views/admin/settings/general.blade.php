@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">General Settings</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="business_name" class="form-label">Business Name</label>
                                    <input type="text" name="business_name" id="business_name" class="form-control" value="{{ old('business_name', $setting->business_name ?? '') }}">
                                    @error('business_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency (e.g. $, €)</label>
                                    <input type="text" name="currency" id="currency" class="form-control" value="{{ old('currency', $setting->currency ?? '') }}">
                                    @error('currency') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="notify_email" class="form-label">Notification Email (Low Stock Alerts)</label>
                                    <input type="email" name="notify_email" id="notify_email" class="form-control" value="{{ old('notify_email', $setting->notify_email ?? '') }}" placeholder="alerts@example.com">
                                    @error('notify_email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="dark_logo" class="form-label">Dark Logo</label>
                                    <input type="file" name="dark_logo" id="dark_logo" class="form-control">
                                    @if(isset($setting->dark_logo))
                                        <img src="{{ asset('storage/'.$setting->dark_logo) }}" alt="Dark Logo" class="img-fluid mt-2 rounded bg-light p-1" style="max-height: 50px;">
                                    @endif
                                    @error('dark_logo') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="light_logo" class="form-label">Light Logo</label>
                                    <input type="file" name="light_logo" id="light_logo" class="form-control">
                                    @if(isset($setting->light_logo))
                                        <img src="{{ asset('storage/'.$setting->light_logo) }}" alt="Light Logo" class="img-fluid mt-2 rounded bg-dark p-1" style="max-height: 50px;">
                                    @endif
                                    @error('light_logo') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="favicon" class="form-label">Favicon</label>
                                    <input type="file" name="favicon" id="favicon" class="form-control">
                                    @if(isset($setting->favicon))
                                        <img src="{{ asset('storage/'.$setting->favicon) }}" alt="Favicon" class="img-fluid mt-2 rounded" style="max-height: 32px;">
                                    @endif
                                    @error('favicon') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="breadcrumb_image" class="form-label">Breadcrumb Image</label>
                                    <input type="file" name="breadcrumb_image" id="breadcrumb_image" class="form-control">
                                    @if(isset($setting->breadcrumb_image))
                                        <img src="{{ asset('storage/'.$setting->breadcrumb_image) }}" alt="Breadcrumb" class="img-fluid mt-2 rounded" style="max-height: 100px;">
                                    @endif
                                    @error('breadcrumb_image') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="login_banner" class="form-label">Login Page Banner (450x600 recommended)</label>
                                    <input type="file" name="login_banner" id="login_banner" class="form-control">
                                    @if(isset($setting->login_banner))
                                        <img src="{{ asset('storage/'.$setting->login_banner) }}" alt="Login Banner" class="img-fluid mt-2 rounded border" style="max-height: 100px;">
                                    @endif
                                    @error('login_banner') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="register_banner" class="form-label">Registration Page Banner (450x650 recommended)</label>
                                    <input type="file" name="register_banner" id="register_banner" class="form-control">
                                    @if(isset($setting->register_banner))
                                        <img src="{{ asset('storage/'.$setting->register_banner) }}" alt="Register Banner" class="img-fluid mt-2 rounded border" style="max-height: 100px;">
                                    @endif
                                    @error('register_banner') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <hr>
                                <h5 class="mb-3">SEO Settings</h5>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title', $setting->meta_title ?? '') }}">
                                    @error('meta_title') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea name="meta_description" id="meta_description" class="form-control" rows="4">{{ old('meta_description', $setting->meta_description ?? '') }}</textarea>
                                    @error('meta_description') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
