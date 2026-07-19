<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
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
            <td>
                <img src="{{ $data->image ? \App\HelperClass::file_url($data->image) : asset('admin_assets/images/users/avatar-1.jpg') }}" alt="" class="avatar-sm rounded-circle me-2">
            </td>
           <td>{{$data->name}}</td>
            <td>{{$data->email}}</td>
            <td>
                @foreach($data->roles as $role)
                    <span class="badge bg-soft-info text-info">{{ $role->name }}</span>
                @endforeach
            </td>
            <td>
                <div class="d-flex gap-2">
                    @can('admins.edit')
                    <a href="{{route('admin.edit', $data->id)}}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="Edit Admin"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                    @endcan
                    @can('admins.delete')
                    <form action="{{route('admin.delete', $data->id)}}" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" data-bs-toggle="tooltip" title="Delete Admin"><iconify-icon icon="solar:trash-bin-trash-broken" class="align-middle fs-18"></iconify-icon></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @endforeach
        @if($users->isEmpty())
            <tr>
                <td colspan="5" class="text-center">No admins found.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    {{ $users->appends(request()->all())->links() }}
</div>
