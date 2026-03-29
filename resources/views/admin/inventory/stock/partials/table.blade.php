<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th style="width: 50px;">#</th>
                <th>Product / Variant</th>
                @if(!Route::is('admin.inventory.damaged.index'))
                    <th>Warehouse</th>
                @endif
                <th>Batch No</th>
                <th class="text-center">Current Stock</th>
                @if(!Route::is('admin.inventory.damaged.index'))
                    <th class="text-center">Min Override</th>
                @endif
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
                        <a href="{{ route('admin.products.show', $level->product_id) }}" class="fw-bold text-primary">
                            {{ $level->product->name }}
                        </a>
                        @if($level->variant)
                            <br><small class="text-muted">Variant: {{ $level->variant->variant_name }}</small>
                        @endif
                    </td>
                    @if(!Route::is('admin.inventory.damaged.index'))
                        <td>
                            <span class="badge {{ $level->warehouse->is_quarantine ? 'bg-danger' : 'badge-soft-info' }}">
                                {{ $level->warehouse->name }}
                            </span>
                        </td>
                    @endif
                    <td>
                        @if($level->batch)
                            <code>{{ $level->batch->batch_number }}</code>
                        @else
                            <span class="text-muted small">N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="fw-bold fs-14 {{ $level->current_quantity <= ($level->min_stock_override ?? 0) ? 'text-danger' : '' }}">
                            {{ $level->current_quantity }}
                        </span>
                    </td>
                    @if(!Route::is('admin.inventory.damaged.index'))
                        <td class="text-center">
                            {{ $level->min_stock_override ?? '-' }}
                        </td>
                    @endif
                    <td>
                        <small class="text-muted">{{ $level->updated_at->format('M d, Y H:i') }}</small>
                    </td>
                    <td class="text-center">
                        @if($level->batch)
                            <a href="{{ route('admin.inventory.batches.show', $level->batch_id) }}" class="btn btn-soft-info btn-sm" title="View Batch Details">
                                <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> Details
                            </a>
                        @else
                            <span class="text-muted small">No Batch</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ Route::is('admin.inventory.damaged.index') ? 5 : 8 }}" class="text-center p-4">
                        <iconify-icon icon="solar:box-minimalistic-broken" class="fs-48 text-muted mb-2"></iconify-icon>
                        <p class="text-muted">No stock records found matching your criteria.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            Showing <span class="fw-semibold">{{ $inventoryLevels->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $inventoryLevels->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $inventoryLevels->total() }}</span> Results
        </div>
        <div>
            {{ $inventoryLevels->appends(request()->all())->links() }}
        </div>
    </div>
</div>
