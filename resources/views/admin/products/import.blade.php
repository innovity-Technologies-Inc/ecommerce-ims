@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Import Products</h4>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i>Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bulk Upload</h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.products.import.template', ['format' => 'csv']) }}" class="btn btn-outline-success btn-sm">
                            <i class="bx bx-download me-1"></i>CSV Template
                        </a>
                        <a href="{{ route('admin.products.import.template', ['format' => 'xlsx']) }}" class="btn btn-success btn-sm">
                            <i class="bx bx-download me-1"></i>XLSX Template
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Choose Excel/CSV File</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx, .xls, .csv">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2">
                                Supported formats: .xlsx, .xls, .csv. Max size: 5MB.
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-upload me-1"></i>Start Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Instructions</h5>
                </div>
                <div class="card-body">
                    <ul class="ps-3 mb-0">
                        <li class="mb-2">Download the template to see the required column format.</li>
                        <li class="mb-2">Ensure Category, Subcategory, and Brand names match exactly with the ones in the system.</li>
                        <li class="mb-2">For products with multiple variants, repeat the product details or leave them blank in subsequent rows with variant details.</li>
                        <li class="mb-2"><strong>Product Name</strong> is used as the unique identifier for creating/updating products.</li>
                        <li class="mb-2">Images cannot be uploaded via bulk import. You can add them later manually.</li>
                        <li>Status should be <strong>active</strong> or <strong>inactive</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
