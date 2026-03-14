@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Social Login Settings</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.social_login.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 mb-4">
                                <h5 class="text-primary border-bottom pb-2">Google Login Configuration</h5>
                            </div>
                            
                            <div class="col-lg-12">
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="google_status" id="google_status" value="1" {{ old('google_status', $setting->google_status ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="google_status">Enable Google Login</label>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="google_client_id" class="form-label">Google Client ID</label>
                                    <input type="text" name="google_client_id" id="google_client_id" class="form-control" value="{{ old('google_client_id', $setting->google_client_id ?? '') }}">
                                    @error('google_client_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="google_client_secret" class="form-label">Google Client Secret</label>
                                    <input type="text" name="google_client_secret" id="google_client_secret" class="form-control" value="{{ old('google_client_secret', $setting->google_client_secret ?? '') }}">
                                    @error('google_client_secret') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="google_redirect_url" class="form-label">Google Redirect URL</label>
                                    <input type="text" name="google_redirect_url" id="google_redirect_url" class="form-control" value="{{ old('google_redirect_url', $setting->google_redirect_url ?? url('/auth/google/callback')) }}" readonly>
                                    <small class="text-muted">Copy this URL and paste it into your Google Console's "Authorized redirect URIs".</small>
                                    @error('google_redirect_url') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">Save Social Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
