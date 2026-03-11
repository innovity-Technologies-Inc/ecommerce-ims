@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Contact Settings</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.contact.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name', $setting->company_name ?? '') }}">
                                    @error('company_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="company_email" class="form-label">Company Email</label>
                                    <input type="email" name="company_email" id="company_email" class="form-control" value="{{ old('company_email', $setting->company_email ?? '') }}">
                                    @error('company_email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $setting->phone_number ?? '') }}">
                                    @error('phone_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea name="address" id="address" class="form-control" rows="4">{{ old('address', $setting->address ?? '') }}</textarea>
                                    @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="map_link" class="form-label">Map (Iframe Source)</label>
                                    <textarea name="map_link" id="map_link" class="form-control" rows="4">{{ old('map_link', $setting->map_link ?? '') }}</textarea>
                                    @error('map_link') <span class="text-danger small">{{ $message }}</span> @enderror
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
