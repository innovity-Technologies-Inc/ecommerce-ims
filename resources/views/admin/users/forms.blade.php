@extends('admin.structure.app')
@section('content')

    <!-- Start Container Fluid -->
    <div class="container-xxl">

        <div class="row">
            <div class="col-lg-12">
                <form action="{{ isset($user) ? route('admin.update', $user->id) : route('admin.register') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if(isset($user))
                        @method('put')
                    @endif
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{$title}}</h4>
                    </div>
                    <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="name" value="{{isset($user) ? $user->name : old('name')}}" required>
                                        @error('name')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="text" id="email" name="email" class="form-control" placeholder="Enter email" value="{{isset($user) ? $user->email : old('email')}}" required>
                                        @error('email')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password">
                                        @error('password')
                                        <span class="small text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" id="confirm_password" name="password_confirmation" class="form-control" placeholder="Enter password">
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
                                            <img src="{{ asset('storage/' . $user->image) }}" alt="Profile Image" class="img-thumbnail" width="100">
                                        </div>
                                    @endif
                                </div>


                                {{--
                                <div class="col-lg-6">
                                    <p>User Status </p>
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="status1" checked="" value="active">
                                            <label class="form-check-label" for="status1">
                                                Active
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="status2" value="inactive">
                                            <label class="form-check-label" for="status2">
                                                In Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                --}}
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
