@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Contact Settings</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.settings.contact.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name', $setting->company_name ?? '') }}">
                                    @error('company_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="company_email" class="form-label">Company Email <span class="text-danger">*</span></label>
                                    <input type="email" name="company_email" id="company_email" class="form-control" value="{{ old('company_email', $setting->company_email ?? '') }}">
                                    @error('company_email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $setting->phone_number ?? '') }}">
                                    @error('phone_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
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

                        <hr class="my-4">
                        <h4 class="card-title mb-4">Social Media Links</h4>
                        
                        <div class="row">
                            @php
                                $socials = [
                                    ['id' => 'facebook', 'label' => 'Facebook'],
                                    ['id' => 'instagram', 'label' => 'Instagram'],
                                    ['id' => 'tiktok', 'label' => 'TikTok'],
                                    ['id' => 'x', 'label' => 'X (Twitter)'],
                                    ['id' => 'thread', 'label' => 'Threads'],
                                    ['id' => 'linkedin', 'label' => 'LinkedIn'],
                                    ['id' => 'whatsapp', 'label' => 'WhatsApp'],
                                    ['id' => 'youtube', 'label' => 'YouTube'],
                                ];
                            @endphp

                            @foreach($socials as $social)
                                <div class="col-lg-6 mb-4">
                                    <div class="card border border-light-subtle h-100 mb-0 shadow-none">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0">{{ $social['label'] }}</h5>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" 
                                                           id="{{ $social['id'] }}_status" name="{{ $social['id'] }}_status" value="1"
                                                           {{ old($social['id'].'_status', $setting->{$social['id'].'_status'} ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $social['id'] }}_status">Visible</label>
                                                </div>
                                            </div>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bxl-{{ $social['id'] === 'x' ? 'twitter' : ($social['id'] === 'thread' ? 'messenger' : $social['id']) }}"></i></span>
                                                <input type="text" name="{{ $social['id'] }}_url" id="{{ $social['id'] }}_url" 
                                                       class="form-control" placeholder="{{ $social['label'] }} Profile URL"
                                                       value="{{ old($social['id'].'_url', $setting->{$social['id'].'_url'} ?? '') }}">
                                            </div>
                                            @error($social['id'].'_url') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-5">Save All Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
