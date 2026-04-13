@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Policy Settings</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.settings.policies.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <label for="privacy_policy" class="form-label fw-bold">Privacy Policy</label>
                        <textarea name="privacy_policy" id="privacy_policy" class="form-control summernote">{{ old('privacy_policy', $setting->privacy_policy ?? '') }}</textarea>
                    </div>

                    <div class="col-lg-12 mb-4">
                        <label for="return_policy" class="form-label fw-bold">Return Policy</label>
                        <textarea name="return_policy" id="return_policy" class="form-control summernote">{{ old('return_policy', $setting->return_policy ?? '') }}</textarea>
                    </div>

                    <div class="col-lg-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">Update Policies</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endsection
