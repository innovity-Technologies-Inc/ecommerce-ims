<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th style="width: 50px;">#</th>
                <th>Batch No</th>
                <th>Product / Variant</th>
                <th>Warehouse</th>
                <th class="text-center text-danger">Damaged Qty</th>
                <th>Last Update</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($inventoryLevels); @endphp
            @forelse($inventoryLevels as $level)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>
                        @if($level->batch)
                            <code>{{ $level->batch->batch_number }}</code>
                        @else
                            <span class="text-muted small">N/A</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.products.show', $level->product_id) }}" class="fw-bold text-primary">
                            {{ $level->product->name }}
                        </a>
                        @if($level->variant)
                            <br><small class="text-muted">Variant: {{ $level->variant->variant_name }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-soft-info">
                            {{ $level->warehouse->name }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="text-danger fw-bold fs-15">{{ $level->damaged_quantity }}</span>
                    </td>
                    <td>
                        <small class="text-muted">{{ $level->updated_at->format('M d, Y H:i') }}</small>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.inventory.damaged.show', $level->id) }}" class="btn btn-soft-danger btn-sm" title="View Damaged Details">
                            <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> Details
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center p-4">
                        <iconify-icon icon="solar:box-minimalistic-broken" class="fs-48 text-muted mb-2"></iconify-icon>
                        <p class="text-muted">No damaged products found.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    {{ $inventoryLevels->appends(request()->all())->links() }}
</div>
