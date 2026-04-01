<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>Avg Performance</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sl = \App\HelperClass::indexNumberSerialization($suppliers);
        @endphp
        @foreach ($suppliers as $data)
        <tr>
            <td>{{$sl++}}</td>
            <td>{{$data->name}}</td>
            <td>{{$data->email}}</td>
            <td>{{$data->mobile}}</td>
            <td>{{$data->address}}</td>
            <td>
                @php
                    $score = $data->average_performance_score;
                    $badgeClass = 'bg-danger';
                    if ($score >= 80) $badgeClass = 'bg-success';
                    elseif ($score >= 50) $badgeClass = 'bg-warning text-dark';
                @endphp
                <span class="badge {{ $badgeClass }}">
                    <iconify-icon icon="solar:star-bold" class="align-middle me-1"></iconify-icon>
                    {{ $score }}%
                </span>
            </td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.suppliers.show', $data->id) }}" class="btn btn-soft-info btn-sm">
                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                    @can('supplier.edit')
                    <a href="{{ route('admin.suppliers.edit', $data->id) }}" class="btn btn-soft-primary btn-sm">
                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                    @endcan
                    @can('supplier.delete')
                    <form method="post" action="{{ route('admin.suppliers.destroy', $data->id) }}">
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
        @if($suppliers->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No suppliers found.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted">
            Showing <span class="fw-semibold">{{ $suppliers->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $suppliers->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $suppliers->total() }}</span> Results
        </div>
        <div>
            {{ $suppliers->appends(request()->all())->links() }}
        </div>
    </div>
</div>
