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
                                    <div class="input-group">
                                        <input type="text" name="google_redirect_url" id="google_redirect_url" class="form-control" value="{{ rtrim(config('app.url'), '/') . '/auth/google/callback' }}" readonly>
                                        <button class="btn btn-secondary" type="button" id="copy-url-btn">
                                            <i class="bx bx-copy"></i> Copy
                                        </button>
                                    </div>
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

@section('scripts')
<script>
    $(document).ready(function() {
        $('#copy-url-btn').on('click', function() {
            const urlInput = document.getElementById('google_redirect_url');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                navigator.clipboard.writeText(urlInput.value).then(() => {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Copied!',
                            text: 'Redirect URL copied to clipboard.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        toastr.success('Redirect URL copied to clipboard!');
                    }
                }).catch(err => {
                    // Fallback for non-secure contexts
                    document.execCommand('copy');
                    toastr.success('Redirect URL copied to clipboard!');
                });
            } catch (err) {
                document.execCommand('copy');
                toastr.success('Redirect URL copied to clipboard!');
            }
        });
    });
</script>
@endsection
