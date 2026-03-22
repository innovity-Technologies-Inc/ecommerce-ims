@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ isset($slider) ? route('admin.sliders.update', $slider->id) : route('admin.sliders.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if(isset($slider))
                        @method('put')
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ isset($slider) ? 'Edit Slider' : 'Add Slider' }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Enter Slider Title" value="{{ old('title', isset($slider) ? $slider->title : '') }}" required>
                                        @error('title')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="subtitle" class="form-label">Subtitle</label>
                                        <input type="text" name="subtitle" id="subtitle" class="form-control" placeholder="Enter Slider Subtitle" value="{{ old('subtitle', isset($slider) ? $slider->subtitle : '') }}">
                                        @error('subtitle')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="subtext" class="form-label">Subtext (over title)</label>
                                        <input type="text" name="subtext" id="subtext" class="form-control" placeholder="Enter Slider Subtext" value="{{ old('subtext', isset($slider) ? $slider->subtext : '') }}">
                                        @error('subtext')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="position" class="form-label">Position</label>
                                        <input type="number" name="position" id="position" class="form-control" value="{{ old('position', isset($slider) ? $slider->position : '0') }}">
                                        @error('position')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="button_name" class="form-label">Button Name</label>
                                        <input type="text" name="button_name" id="button_name" class="form-control" placeholder="Enter Button Name" value="{{ old('button_name', isset($slider) ? $slider->button_name : '') }}">
                                        @error('button_name')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="button_url" class="form-label">Button URL</label>
                                        <input type="text" name="button_url" id="button_url" class="form-control" placeholder="Enter Button URL" value="{{ old('button_url', isset($slider) ? $slider->button_url : '') }}">
                                        @error('button_url')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', isset($slider) ? $slider->is_active : true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active Status</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Slider Image <span class="text-danger">*</span></label>
                                        <input type="file" name="image" id="image" class="filepond">

                                        @if(isset($slider) && $slider->image)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/'.$slider->image) }}" alt="{{ $slider->title }}" class="avatar-lg rounded">
                                            </div>
                                        @endif

                                        @error('image')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top">
                            <button type="submit" class="btn btn-primary">{{ isset($slider) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
