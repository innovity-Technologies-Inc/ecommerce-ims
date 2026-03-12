<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
        <tr>
            <th>#</th>
            <th>Icon</th>
            <th>Name</th>
            <th>Slug</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sl = \App\HelperClass::indexNumberSerialization($brands);
        @endphp
        @foreach ($brands as $data)
        <tr>
            <td>{{$sl++}}</td>
            <td>
                @if($data->icon)
                    <img src="{{ asset('storage/'.$data->icon) }}" alt="{{ $data->name }}" class="avatar-sm rounded">
                @else
                    <span class="text-muted">No Icon</span>
                @endif
            </td>
            <td>{{$data->name}}</td>
            <td>{{$data->slug}}</td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.brands.edit', $data->id) }}" class="btn btn-soft-primary btn-sm">
                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                    <form method="post" action="{{ route('admin.brands.destroy', $data->id) }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete">
                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        @if($brands->isEmpty())
            <tr>
                <td colspan="5" class="text-center">No brands found.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted">
            Showing <span class="fw-semibold">{{ $brands->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $brands->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $brands->total() }}</span> Results
        </div>
        <div>
            {{ $brands->appends(request()->all())->links() }}
        </div>
    </div>
</div>
