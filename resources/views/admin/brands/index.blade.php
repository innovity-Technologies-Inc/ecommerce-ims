@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Brands</h4>
            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm">Add Brand</a>
        </div>

        <div class="card overflow-hidden">
            <div class="card-body p-0">
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
                        @php($sl = \App\HelperClass::indexNumberSerialization($brands))
                        @foreach($brands as $data)
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
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer border-top">
                {{ $brands->links() }}
            </div>
        </div>
    </div>

@endsection
