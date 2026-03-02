@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Product List</h4>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">Add Product</a>
    </div>

    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover table-centered">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price Range</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($sl = \App\HelperClass::indexNumberSerialization($products))
                        @foreach($products as $data)
                        <tr>
                            <td>{{$sl++}}</td>
                            <td>
                                @if($data->primaryImage)
                                    <img src="{{ asset('storage/'.$data->primaryImage->image_path) }}" alt="{{ $data->name }}" class="avatar-sm rounded">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>{{$data->name}}</td>
                            <td>{{ $data->category ? $data->category->name : '-' }}</td>
                            <td>
                                @if($data->variants->count() > 0)
                                    ${{ number_format($data->variants->min('price'), 2) }} - ${{ number_format($data->variants->max('price'), 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.products.show', $data->id) }}" class="btn btn-soft-info btn-sm">
                                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $data->id) }}" class="btn btn-soft-primary btn-sm">
                                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $data->id) }}" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete">
                                            <iconify-icon icon="solar:trash-bin-trash-broken" class="align-middle fs-18"></iconify-icon>
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
            {{ $products->links() }}
        </div>
    </div>
</div>

@endsection
