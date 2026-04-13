@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">{{ isset($faq) ? 'Edit FAQ' : 'Add New FAQ' }}</h4>
        <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($faq) ? route('admin.faqs.update', $faq->id) : route('admin.faqs.store') }}" method="POST">
                @csrf
                @if(isset($faq))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <label for="question" class="form-label">Question</label>
                        <input type="text" name="question" id="question" class="form-control" value="{{ old('question', $faq->question ?? '') }}" required>
                        @error('question')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-12 mb-3">
                        <label for="answer" class="form-label">Answer</label>
                        <textarea name="answer" id="answer" class="form-control" rows="5" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
                        @error('answer')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-6 mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
                    </div>

                    <div class="col-lg-6 mb-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="col-lg-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">{{ isset($faq) ? 'Update FAQ' : 'Save FAQ' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
