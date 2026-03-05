@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Sliders</h4>
            <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-sm">Add</a>
        </div>

        <div class="card overflow-hidden">
            <div class="card-body p-0">
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
                                    <a href="{{ route('admin.sliders.edit', $data->id) }}" class="btn btn-soft-primary btn-sm">
                                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                    <form method="post" action="{{ route('admin.sliders.destroy', $data->id) }}">
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
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted">
                        Showing <span class="fw-semibold">{{ $sliders->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $sliders->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $sliders->total() }}</span> Results
                    </div>
                    <div>
                        {{ $sliders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
