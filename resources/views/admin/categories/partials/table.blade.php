<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
        <tr>
            <th>#</th>
            <th>Icon</th>
            <th>Name</th>
            <th>Parent</th>
            <th>Slug</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sl = \App\HelperClass::indexNumberSerialization($categories);
        @endphp
        @foreach ($categories as $data)
        <tr>
            <td>{{$sl++}}</td>
            <td>
                @if($data->icon)
                    <img src="{{ \App\HelperClass::file_url($data->icon) }}" alt="{{ $data->name }}" class="avatar-sm rounded">
                @else
                    <span class="text-muted">No Icon</span>
                @endif
            </td>
            <td>{{$data->name}}</td>
            <td>{{ $data->parent ? $data->parent->name : '-' }}</td>
            <td>{{$data->slug}}</td>
            <td>
                <div class="form-check form-switch">
                    <input class="form-check-input status-toggle" type="checkbox" role="switch" 
                        data-id="{{ $data->id }}" {{ $data->status ? 'checked' : '' }} {{ auth('admin')->user()->can('category.edit') ? '' : 'disabled' }}>
                </div>
            </td>
            <td>
                <div class="d-flex gap-2">
                    @can('category.edit')
                    <a href="{{ route('admin.categories.edit', $data->id) }}" class="btn btn-soft-primary btn-sm">
                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                    @endcan
                    @can('category.delete')
                    <form method="post" action="{{ route('admin.categories.destroy', $data->id) }}">
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
        @if($categories->isEmpty())
            <tr>
                <td colspan="7" class="text-center">No categories found.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    {{ $categories->appends(request()->all())->links() }}
</div>
