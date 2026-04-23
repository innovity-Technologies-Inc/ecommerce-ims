@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Generate Payslip</h4>
        <a href="{{ route('admin.hrm.payslip.index') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <form action="{{ route('admin.hrm.payslip.generate') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select name="admin_id" class="form-control select2" required>
                                <option value="">Select Employee</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ old('admin_id') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                                @endforeach
                            </select>
                            @error('admin_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-01')) }}" required>
                                    @error('start_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', date('Y-m-t')) }}" required>
                                    @error('end_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                            Payslip will be calculated based on the employee's salary settings and recorded attendance for the selected date range.
                        </div>
                    </div>
                    <div class="card-footer border-top">
                        <button type="submit" class="btn btn-primary w-100">Generate Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection
