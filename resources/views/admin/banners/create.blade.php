@extends('admin.structure.app')

@section('title', 'Add New Banner')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Add New Banner</h4>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light-subtle">
                    <h5 class="card-title mb-0">Banner Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">Banner Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                    value="{{ old('slug') }}" placeholder="e.g. home_1_left, promo_banner" required>
                                <small class="text-muted">Unique placement slug used to fetch this banner in the frontend code.</small>
                                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Image <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="filepond @error('image') is-invalid @enderror" required>
                                <div class="mt-2 alert alert-soft-warning border-0">
                                    <i class="bx bx-info-circle me-1"></i> 
                                    Allowed formats: JPEG, PNG, JPG, WEBP, AVIF. Max: 2MB.
                                </div>
                                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Redirect Link (Optional)</label>
                                <input type="text" name="link" class="form-control @error('link') is-invalid @enderror" 
                                    value="{{ old('link') }}" placeholder="https://example.com or #">
                                <small class="text-muted">Enter where the user will be redirected upon clicking the banner.</small>
                                @error('link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 pt-2">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bx bx-save me-1"></i> Create Banner
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
