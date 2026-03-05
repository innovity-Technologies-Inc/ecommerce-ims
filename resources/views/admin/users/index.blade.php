@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Admins</h4>
            <a href="{{ route('admin.create') }}" class="btn btn-primary btn-sm">Add</a>
        </div>

        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $sl = \App\HelperClass::indexNumberSerialization($users);
                        @endphp
                        @foreach ($users as $data)
                        <tr>
                            <td>{{$sl++}}</td>
                           <td>{{$data->name}}</td>
                            <td>{{$data->email}}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{route('admin.edit', $data->id)}}" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                    <form method="post" action="{{route('admin.delete', $data->id)}}">
                                        @csrf
                                        @method('delete')
                                    <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            <div class="card-footer border-top">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted">
                        Showing <span class="fw-semibold">{{ $users->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $users->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $users->total() }}</span> Results
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div> <!-- end card -->

    </div>


@endsection
