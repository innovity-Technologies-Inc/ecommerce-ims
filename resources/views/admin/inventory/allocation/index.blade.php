@extends('admin.structure.app')

@section('title', 'Inventory Allocation')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Unallocated Stock</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Allocation</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4">The list below shows received products and variants that have not yet been allocated to a warehouse.</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Variant Name</th>
                                    <th>SKU</th>
                                    <th class="text-center">Unallocated Stock</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unallocated as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>
                                            @if($item['variant_name'] !== 'N/A')
                                                <span class="badge badge-soft-info">{{ $item['variant_name'] }}</span>
                                            @else
                                                <span class="text-muted">No Variant</span>
                                            @endif
                                        </td>
                                        <td>{{ $item['sku'] }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary fs-12">{{ $item['stock'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.inventory.allocation.create', ['product_id' => $item['id'], 'variant_id' => $item['variant_id']]) }}" class="btn btn-soft-success btn-sm">
                                                <i class="bx bx-plus-circle me-1"></i> Allocate
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No unallocated stock found. All received products are assigned to warehouses.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
