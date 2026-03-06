@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('admin.sections.update', $section->section_name) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ $title }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="section_title" class="form-label">Section Title</label>
                                        <input type="text" name="section_title" id="section_title" class="form-control" value="{{ old('section_title', $section->section_title) }}" required>
                                        @error('section_title')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="section_subtitle" class="form-label">Section Subtitle</label>
                                        <input type="text" name="section_subtitle" id="section_subtitle" class="form-control" value="{{ old('section_subtitle', $section->section_subtitle) }}">
                                        @error('section_subtitle')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="limit" class="form-label">Product Limit</label>
                                        <input type="number" name="limit" id="limit" class="form-control" value="{{ old('limit', $section->limit) }}" required>
                                        @error('limit')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label d-block">Selection Mode</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mode" id="mode_organic" value="organic" {{ old('mode', $section->mode) === 'organic' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mode_organic">Organic (By Product Flag)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mode" id="mode_custom" value="custom" {{ old('mode', $section->mode) === 'custom' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mode_custom">Custom (Select Manually)</label>
                                        </div>
                                        @error('mode')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                @if($section->section_name === 'featured')
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="background_image" class="form-label">Background Image</label>
                                        <input type="file" name="background_image" id="background_image" class="filepond">
                                        @if($section->background_image)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/'.$section->background_image) }}" alt="background" class="avatar-lg rounded">
                                            </div>
                                        @endif
                                        @error('background_image')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" name="is_visible" id="is_visible" {{ old('is_visible', $section->is_visible) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_visible">Show Section on Homepage</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 {{ old('mode', $section->mode) === 'custom' ? '' : 'd-none' }}" id="custom_product_selection">
                                    <div class="mb-3">
                                        <label for="product_ids" class="form-label">Select Products (Only filtered by flag if applicable)</label>
                                        <select name="product_ids[]" id="product_ids" class="form-control select2_list" multiple>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ in_array($product->id, old('product_ids', $selectedProducts->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('product_ids')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
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
            $('input[name="mode"]').change(function() {
                if ($(this).val() === 'custom') {
                    $('#custom_product_selection').removeClass('d-none');
                } else {
                    $('#custom_product_selection').addClass('d-none');
                }
            });
        });
    </script>
@endsection
