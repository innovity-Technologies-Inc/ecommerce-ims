@extends('admin.structure.app')
@section('content')

    <!-- Start Container Fluid -->
    <div class="container-xxl">

        <div class="row">
            <div class="col-lg-12">
                <form action="{{ isset($user) ? route('admin.update', $user->id) : route('admin.register') }}" method="post">
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
                                {{--<div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="workspace" class="form-label">Add Workspace</label>
                                            <select class="form-control" id="workspace" data-choices data-choices-groups data-placeholder="Select Workspace">
                                                <option value="Select Workspace"></option>
                                                <option value="Facebook">Facebook</option>
                                                <option value="Slack">Slack</option>
                                                <option value="Zoom">Zoom</option>
                                                <option value="Analytics">Analytics</option>
                                                <option value="Meet">Meet</option>
                                                <option value="Mail">Mail</option>
                                                <option value="Strip">Strip</option>
                                            </select>
                                        </div>
                                </div>--}}

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
                                    </div>
                                    @error('password')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" id="confirm_password" name="password_confirmation" class="form-control" placeholder="Enter password">
                                    </div>
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
