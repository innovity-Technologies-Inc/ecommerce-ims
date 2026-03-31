<div class="table-responsive">
    <table class="table align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 50px;">#SL</th>
                <th>Adjustment Number</th>
                <th>Date</th>
                <th>Warehouse</th>
                <th>Batch Number</th>
                <th>Created By</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($adjustments); @endphp
            @forelse($adjustments as $adjustment)
                <tr>
                    <td class="text-center">{{ $sl++ }}</td>
                    <td><span class="fw-medium">{{ $adjustment->adjustment_number }}</span></td>
                    <td>{{ $adjustment->adjustment_date->format('d M, Y') }}</td>
                    <td>{{ $adjustment->warehouse->name }}</td>
                    <td>{{ $adjustment->batch->batch_number }}</td>
                    <td>{{ $adjustment->creator->name }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.inventory.adjustment.show', $adjustment->id) }}" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-show"></i> View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center p-5">
                        <div class="text-muted">No stock adjustments found.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($adjustments->hasPages())
<div class="card-footer border-top-0">
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted">
            Showing {{ $adjustments->firstItem() }} to {{ $adjustments->lastItem() }} of {{ $adjustments->total() }} results
        </div>
        <div class="pagination-container">
            {{ $adjustments->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endif
