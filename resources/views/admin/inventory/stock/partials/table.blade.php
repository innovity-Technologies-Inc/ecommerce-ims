<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th style="width: 50px;">#</th>
                <th>Product / Variant</th>
                <th>Warehouse</th>
                <th>Batch No</th>
                <th class="text-center">Current Stock</th>
                <th class="text-center">Min Override</th>
                <th>Last Update</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventoryLevels as $level)
                <tr>
                    <td>{{ \App\HelperClass::indexNumberSerialization($inventoryLevels)[$loop->index] }}</td>
                    <td>
                        <a href="{{ route('admin.products.show', $level->product_id) }}" class="fw-bold text-primary">
                            {{ $level->product->name }}
                        </a>
                        @if($level->variant)
                            <br><small class="text-muted">Variant: {{ $level->variant->variant_name }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $level->warehouse->is_quarantine ? 'bg-danger' : 'badge-soft-info' }}">
                            {{ $level->warehouse->name }}
                        </span>
                    </td>
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
                    <td class="text-center">
                        {{ $level->min_stock_override ?? '-' }}
                    </td>
                    <td>
                        <small class="text-muted">{{ $level->updated_at->format('M d, Y H:i') }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center p-4">
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
