@extends('admin.structure.app')

@section('title', 'Allocate Stock')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Allocate Stock to Warehouse</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.inventory.allocation.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.inventory.allocation.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="product_variant_id" value="{{ $variant ? $variant->id : '' }}">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="text-muted mb-1">Product:</label>
                                <div class="fw-bold fs-16">{{ $product->name }}</div>
                            </div>
                            @if($variant)
                            <div class="col-md-6">
                                <label class="text-muted mb-1">Variant:</label>
                                <div class="fw-bold fs-16">{{ $variant->variant_name }}</div>
                            </div>
                            @endif
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded text-center">
                                    <label class="text-muted mb-1 d-block">Available Unallocated Stock:</label>
                                    <span class="fs-24 fw-bold text-primary">{{ $variant ? $variant->stock : $product->stock }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">Select Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select select2" required>
                                <option value="">-- Choose Warehouse --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }} ({{ $warehouse->location }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="quantity" class="form-label">Allocation Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control" 
                                   min="1" max="{{ $variant ? $variant->stock : $product->stock }}" 
                                   value="{{ $variant ? $variant->stock : $product->stock }}" required>
                            <small class="text-muted">Maximum available: {{ $variant ? $variant->stock : $product->stock }}</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.inventory.allocation.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Allocate Stock</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Choose Warehouse --",
            allowClear: true
        });
    });
</script>
@endsection
