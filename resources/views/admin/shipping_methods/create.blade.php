@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create Shipping Method</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.shipping_methods.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Standard Shipping" value="{{ old('name') }}" required>
                                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" id="price" name="price" class="form-control" placeholder="0.00" value="{{ old('price') }}" required>
                                        @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="short_description" class="form-label">Short Description</label>
                                        <textarea id="short_description" name="short_description" class="form-control" rows="3" placeholder="Description shown on cart page">{{ old('short_description') }}</textarea>
                                        @error('short_description') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select id="status" name="status" class="form-select">
                                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.shipping_methods.index') }}" class="btn btn-soft-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
