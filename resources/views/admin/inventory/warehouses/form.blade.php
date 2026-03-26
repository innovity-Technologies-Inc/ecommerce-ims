@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ isset($warehouse) ? route('admin.warehouses.update', $warehouse->id) : route('admin.warehouses.store') }}" method="post">
                    @csrf
                    @if(isset($warehouse))
                        @method('put')
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ isset($warehouse) ? 'Edit Warehouse' : 'Add Warehouse' }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Warehouse Name" value="{{ old('name', $warehouse->name ?? '') }}" required>
                                        @error('name')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <textarea name="location" id="location" class="form-control" rows="3" placeholder="Enter Warehouse Location">{{ old('location', $warehouse->location ?? '') }}</textarea>
                                        @error('location')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="is_quarantine" value="0">
                                            <input class="form-check-input" type="checkbox" name="is_quarantine" id="is_quarantine" value="1" {{ old('is_quarantine', $warehouse->is_quarantine ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_quarantine">Is Quarantine Warehouse? (Damaged products will be stored here)</label>
                                        </div>
                                        @error('is_quarantine')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top">
                            <button type="submit" class="btn btn-primary">{{ isset($warehouse) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
