<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
        <tr>
            <th>#</th>
            <th>Image</th>
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
            <td>
                <img src="{{ $data->image ? asset('storage/' . $data->image) : asset('admin_assets/images/users/avatar-1.jpg') }}" alt="" class="avatar-sm rounded-circle me-2">
            </td>
           <td>{{$data->name}}</td>
            <td>{{$data->email}}</td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{route('admin.edit', $data->id)}}" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                    <form action="{{route('admin.delete', $data->id)}}" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete"><iconify-icon icon="solar:trash-bin-trash-broken" class="align-middle fs-18"></iconify-icon></button>
                    </form>
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
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted">
            Showing <span class="fw-semibold">{{ $users->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $users->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $users->total() }}</span> Results
        </div>
        <div>
            {{ $users->appends(request()->all())->links() }}
        </div>
    </div>
</div>
