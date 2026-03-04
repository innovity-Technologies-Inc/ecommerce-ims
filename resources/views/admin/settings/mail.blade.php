@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Mail Settings</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.mail.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="mail_mailer" class="form-label">Mail Mailer</label>
                                    <input type="text" name="mail_mailer" id="mail_mailer" class="form-control" value="{{ old('mail_mailer', $setting->mail_mailer ?? 'smtp') }}" placeholder="smtp">
                                    @error('mail_mailer') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="mail_host" class="form-label">Mail Host</label>
                                    <input type="text" name="mail_host" id="mail_host" class="form-control" value="{{ old('mail_host', $setting->mail_host ?? '') }}" placeholder="smtp.mailtrap.io">
                                    @error('mail_host') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="mail_port" class="form-label">Mail Port</label>
                                    <input type="text" name="mail_port" id="mail_port" class="form-control" value="{{ old('mail_port', $setting->mail_port ?? '') }}" placeholder="2525">
                                    @error('mail_port') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="mail_encryption" class="form-label">Mail Encryption</label>
                                    <input type="text" name="mail_encryption" id="mail_encryption" class="form-control" value="{{ old('mail_encryption', $setting->mail_encryption ?? '') }}" placeholder="tls">
                                    @error('mail_encryption') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="mail_username" class="form-label">Mail Username</label>
                                    <input type="text" name="mail_username" id="mail_username" class="form-control" value="{{ old('mail_username', $setting->mail_username ?? '') }}">
                                    @error('mail_username') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="mail_password" class="form-label">Mail Password</label>
                                    <input type="password" name="mail_password" id="mail_password" class="form-control" value="{{ old('mail_password', $setting->mail_password ?? '') }}">
                                    @error('mail_password') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="mail_from_address" class="form-label">Mail From Address</label>
                                    <input type="email" name="mail_from_address" id="mail_from_address" class="form-control" value="{{ old('mail_from_address', $setting->mail_from_address ?? '') }}" placeholder="hello@example.com">
                                    @error('mail_from_address') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="mail_from_name" class="form-label">Mail From Name</label>
                                    <input type="text" name="mail_from_name" id="mail_from_name" class="form-control" value="{{ old('mail_from_name', $setting->mail_from_name ?? '') }}" placeholder="Example App">
                                    @error('mail_from_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Mail Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
