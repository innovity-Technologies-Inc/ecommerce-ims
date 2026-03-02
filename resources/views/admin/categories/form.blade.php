@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if(isset($category))
                        @method('put')
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ isset($category) ? 'Edit Category' : 'Add Category' }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Category Name</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Category Name" value="{{ old('name', isset($category) ? $category->name : '') }}" required>
                                        @error('name')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="parent_id" class="form-label">Parent Category (Optional)</label>
                                        <select name="parent_id" id="parent_id" class="form-control select2_list">
                                            <option value="">None (Top Level)</option>
                                            @foreach($parentCategories as $parent)
                                                <option value="{{ $parent->id }}" {{ old('parent_id', isset($category) ? $category->parent_id : '') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="icon" class="form-label">Category Icon</label>
                                        <input type="file" name="icon" id="icon" class="filepond">

                                        @if(isset($category) && $category->icon)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/'.$category->icon) }}" alt="{{ $category->name }}" class="avatar-lg rounded">
                                            </div>
                                        @endif

                                        @error('icon')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>                            </div>
                        </div>
                        <div class="card-footer border-top">
                            <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
