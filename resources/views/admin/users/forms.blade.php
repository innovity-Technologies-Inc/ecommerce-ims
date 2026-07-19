@extends('admin.structure.app')
@section('content')

    <!-- Start Container Fluid -->
    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">{{ $title }}</h4>
            <a href="{{ route('admin.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <form action="{{ isset($user) ? route('admin.update', $user->id) : route('admin.register') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if(isset($user))
                        @method('put')
                    @endif
                <div class="card">
                    <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="employee_id" class="form-label">Employee ID</label>
                                        <input type="text" name="employee_id" id="employee_id" class="form-control" placeholder="e.g. SE-001" value="{{isset($user) ? $user->employee_id : old('employee_id')}}">
                                        @error('employee_id')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="name" value="{{isset($user) ? $user->name : old('name')}}" required>
                                        @error('name')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="designation" class="form-label">Designation</label>
                                        <input type="text" name="designation" id="designation" class="form-control" placeholder="e.g. Software Engineer" value="{{isset($user) ? $user->designation : old('designation')}}">
                                        @error('designation')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="text" id="email" name="email" class="form-control" placeholder="Enter email" value="{{isset($user) ? $user->email : old('email')}}" required>
                                        @error('email')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password @if(!isset($user)) <span class="text-danger">*</span> @endif</label>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password">
                                        @error('password')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password @if(!isset($user)) <span class="text-danger">*</span> @endif</label>
                                        <input type="password" id="confirm_password" name="password_confirmation" class="form-control" placeholder="Enter password">
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input toggle-password-visibility" id="show-password-user">
                                        <label class="form-check-label" for="show-password-user">Show Passwords</label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Assign Role <span class="text-danger">*</span></label>
                                        <select name="role" id="role" class="form-control" required>
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ (isset($user) && $user->hasRole($role->name)) || old('role') == $role->name ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Profile Image</label>
                                        <input type="file" id="image" name="image" class="form-control">
                                        @error('image')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                    @if(isset($user) && $user->image)
                                        <div class="mb-3">
                                            <img src="{{ \App\HelperClass::file_url($user->image) }}" alt="Profile Image" class="img-thumbnail rounded-circle avatar-xl">
                                        </div>
                                    @endif
                                </div>

                                <div class="col-lg-12">
                                    <hr>
                                    <h5 class="mb-3 text-primary">HRM Settings</h5>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="is_time_tracking" class="form-check-input" id="is_time_tracking" value="1" {{ (isset($user) && $user->is_time_tracking) || old('is_time_tracking') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_time_tracking">Enable Time Tracking</label>
                                        @error('is_time_tracking')
                                        <div class="small text-danger">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="salary_amount" class="form-label">Hourly Salary Rate ({{ \App\HelperClass::generalSettings()->currency ?? '$' }})</label>
                                        <input type="number" name="salary_amount" id="salary_amount" class="form-control" step="0.01" placeholder="0.00" value="{{isset($user) ? $user->salary_amount : old('salary_amount', 0)}}">
                                        <small class="text-muted">Set the amount paid per 1 hour of work.</small>
                                        @error('salary_amount')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="daily_work_hours" class="form-label">Standard Daily Work Hours</label>
                                        <input type="number" name="daily_work_hours" id="daily_work_hours" class="form-control" step="0.1" placeholder="8.0" value="{{isset($user) ? $user->daily_work_hours : old('daily_work_hours', 8)}}">
                                        @error('daily_work_hours')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card-footer border-top">
                        <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }}</button>
                    </div>
                </div>
                </form>

            </div>

        </div>


    </div>
    <!-- End Container Fluid -->


@endsection
