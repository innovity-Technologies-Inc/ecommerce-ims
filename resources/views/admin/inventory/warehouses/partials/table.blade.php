<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Location</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sl = \App\HelperClass::indexNumberSerialization($warehouses);
        @endphp
        @foreach ($warehouses as $data)
        <tr>
            <td>{{$sl++}}</td>
            <td>{{$data->name}}</td>
            <td>{{$data->location}}</td>
            <td>
                <div class="d-flex gap-2">
                    @can('warehouse.edit')
                    <a href="{{ route('admin.warehouses.edit', $data->id) }}" class="btn btn-soft-primary btn-sm">
                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                    @endcan
                    @can('warehouse.delete')
                    <form method="post" action="{{ route('admin.warehouses.destroy', $data->id) }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete">
                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                        </button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @endforeach
        @if($warehouses->isEmpty())
            <tr>
                <td colspan="4" class="text-center">No warehouses found.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted">
            Showing <span class="fw-semibold">{{ $warehouses->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $warehouses->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $warehouses->total() }}</span> Results
        </div>
        <div>
            {{ $warehouses->appends(request()->all())->links() }}
        </div>
    </div>
</div>
