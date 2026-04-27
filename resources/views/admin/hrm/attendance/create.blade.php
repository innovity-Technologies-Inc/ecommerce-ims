@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Record Attendance</h4>
            <p class="text-muted mb-0">Manually record work hours for an employee.</p>
        </div>
        <a href="{{ route('admin.hrm.attendance.index') }}" class="btn btn-secondary d-flex align-items-center gap-1">
            <iconify-icon icon="solar:arrow-left-bold-duotone"></iconify-icon> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.hrm.attendance.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="admin_id" class="form-label">Employee <span class="text-danger">*</span></label>
                            <select name="admin_id" id="admin_id" class="form-select select2_list" required>
                                <option value="">Select Employee</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ old('admin_id') == $admin->id ? 'selected' : '' }}>{{ $admin->name }} ({{ $admin->email }})</option>
                                @endforeach
                            </select>
                            @error('admin_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                            @error('date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="clock_in" class="form-label">Clock In Time <span class="text-danger">*</span></label>
                            <input type="time" name="clock_in" id="clock_in" class="form-control" value="{{ old('clock_in', now()->format('H:i')) }}" required>
                            @error('clock_in') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="clock_out" class="form-label">Clock Out Time <span class="text-danger">*</span></label>
                            <input type="time" name="clock_out" id="clock_out" class="form-control" value="{{ old('clock_out', now()->addHours(8)->format('H:i')) }}" required>
                            @error('clock_out') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="reset" class="btn btn-soft-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
