@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Generate Bulk Payslips</h4>
            <p class="text-muted mb-0">Create salary records for all employees for a specific period.</p>
        </div>
        <a href="{{ route('admin.hrm.payslip.index') }}" class="btn btn-secondary">
            <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.hrm.payslip.generate') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="title" class="form-label">Generation Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="e.g. April 2026 Week - 1" value="{{ old('title') }}" required>
                            <div class="form-text">Give this batch a descriptive name.</div>
                            @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>
                                @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required>
                                @error('end_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="alert alert-info border-0 mb-4">
                            <div class="d-flex align-items-center">
                                <iconify-icon icon="solar:info-circle-bold-duotone" class="fs-24 me-2"></iconify-icon>
                                <div>
                                    <strong>How it works:</strong> The system will automatically scan all employee attendance records within this date range and calculate their net salary based on their individual hourly rates.
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <iconify-icon icon="solar:play-circle-bold-duotone" class="me-1"></iconify-icon> Generate All Payslips
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
