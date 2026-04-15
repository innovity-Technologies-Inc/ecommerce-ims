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
            <td>{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{number_format($data->price, 2)}}</td>
            <td>{{ \Illuminate\Support\Str::limit($data->short_description, 50) }}</td>
            <td>
                <div class="form-check form-switch">
                    <input class="form-check-input status-toggle" type="checkbox" role="switch" 
                        data-id="{{ $data->id }}" {{ $data->status ? 'checked' : '' }} {{ auth('admin')->user()->can('shipping_methods.edit') ? '' : 'disabled' }}>
                </div>
            </td>
            <td>
                <div class="d-flex gap-2">
                    @can('shipping_methods.edit')
                    <a href="{{ route('admin.shipping_methods.edit', $data->id) }}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="Edit Shipping Method">
                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                    @endcan
                    @can('shipping_methods.delete')
                    <form method="post" action="{{ route('admin.shipping_methods.destroy', $data->id) }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" data-bs-toggle="tooltip" title="Delete Shipping Method">
                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                        </button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @endforeach
        @if($shippingMethods->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No shipping methods found.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    {{ $shippingMethods->appends(request()->all())->links() }}
</div>
