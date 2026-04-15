<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Title</th>
            <th>Subtitle</th>
            <th>Position</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sl = \App\HelperClass::indexNumberSerialization($sliders);
        @endphp
        @foreach ($sliders as $data)
        <tr>
            <td>{{$sl++}}</td>
            <td>
                <img src="{{ asset('storage/'.$data->image) }}" alt="{{ $data->title }}" class="avatar-lg rounded">
            </td>
            <td>
                <p class="mb-0 fw-bold">{{ $data->title }}</p>
                <small class="text-muted">{{ $data->subtext }}</small>
            </td>
            <td>{{ $data->subtitle }}</td>
            <td>{{ $data->position }}</td>
            <td>
                @if($data->is_active)
                    <span class="badge bg-success-subtle text-success">Active</span>
                @else
                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-2">
                    @can('sliders.edit')
                    <a href="{{ route('admin.sliders.edit', $data->id) }}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="Edit Slider">
                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                    @endcan
                    @can('sliders.delete')
                    <form method="post" action="{{ route('admin.sliders.destroy', $data->id) }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" data-bs-toggle="tooltip" title="Delete Slider">
                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                        </button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @endforeach
        @if($sliders->isEmpty())
            <tr>
                <td colspan="7" class="text-center">No sliders found.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    {{ $sliders->appends(request()->all())->links() }}
</div>
