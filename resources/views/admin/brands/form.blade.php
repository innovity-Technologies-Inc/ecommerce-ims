@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ isset($brand) ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if(isset($brand))
                        @method('put')
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ isset($brand) ? 'Edit Brand' : 'Add Brand' }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Brand Name</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Brand Name" value="{{ old('name', $brand->name ?? '') }}" >
                                        @error('name')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="icon" class="form-label">Brand Icon</label>
                                        <input type="file" name="icon" id="icon" class="filepond">

                                        @if(isset($brand) && $brand->icon)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/'.$brand->icon) }}" alt="{{ $brand->name }}" class="avatar-lg rounded">
                                            </div>
                                        @endif

                                        @error('icon')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>                            </div>
                        </div>
                        <div class="card-footer border-top">
                            <button type="submit" class="btn btn-primary">{{ isset($brand) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
