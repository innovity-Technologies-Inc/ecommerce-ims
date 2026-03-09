@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Shipping Methods</h4>
            <a href="{{ route('admin.shipping_methods.create') }}" class="btn btn-primary btn-sm">Add</a>
        </div>

        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Short Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $sl = \App\HelperClass::indexNumberSerialization($shippingMethods);
                        @endphp
                        @foreach ($shippingMethods as $data)
                        <tr>
                            <td>{{$sl++}}</td>
                            <td>{{$data->name}}</td>
                            <td>${{number_format($data->price, 2)}}</td>
                            <td>{{ \Illuminate\Support\Str::limit($data->short_description, 50) }}</td>
                            <td>
                                <span class="badge {{ $data->status ? 'bg-success' : 'bg-danger' }} text-white">
                                    {{ $data->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.shipping_methods.edit', $data->id) }}" class="btn btn-soft-primary btn-sm">
                                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                    <form method="post" action="{{ route('admin.shipping_methods.destroy', $data->id) }}">
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
                        Showing <span class="fw-semibold">{{ $shippingMethods->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $shippingMethods->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $shippingMethods->total() }}</span> Results
                    </div>
                    <div>
                        {{ $shippingMethods->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
