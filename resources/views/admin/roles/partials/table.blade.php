<div class="table-responsive">
    <table class="table table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th class="ps-3">#</th>
                <th>Role Name</th>
                <th class="text-end pe-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($roles); @endphp
            @forelse($roles as $role)
                <tr>
                    <td class="ps-3">{{ $sl++ }}</td>
                    <td>{{ $role->name }}</td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-2">
                            @can('roles.edit')
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-soft-primary btn-sm" title="Edit">
                                <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                            </a>
                            @endcan
                            @can('roles.delete')
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" title="Delete">
                                    <iconify-icon icon="solar:trash-bin-trash-broken" class="align-middle fs-18"></iconify-icon>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No roles found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($roles->hasPages())
    <div class="card-footer border-top-0">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-muted">
                Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} Results
            </div>
            {{ $roles->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
