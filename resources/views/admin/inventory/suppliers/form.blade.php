@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ isset($supplier) ? route('admin.suppliers.update', $supplier->id) : route('admin.suppliers.store') }}" method="post">
                    @csrf
                    @if(isset($supplier))
                        @method('put')
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ isset($supplier) ? 'Edit Supplier' : 'Add Supplier' }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Supplier Name" value="{{ old('name', $supplier->name ?? '') }}" required>
                                        @error('name')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" value="{{ old('email', $supplier->email ?? '') }}">
                                        @error('email')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="mobile" class="form-label">Mobile</label>
                                        <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile Number" value="{{ old('mobile', $supplier->mobile ?? '') }}">
                                        @error('mobile')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea name="address" id="address" class="form-control" rows="3" placeholder="Enter Supplier Address">{{ old('address', $supplier->address ?? '') }}</textarea>
                                        @error('address')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top">
                            <button type="submit" class="btn btn-primary">{{ isset($supplier) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
