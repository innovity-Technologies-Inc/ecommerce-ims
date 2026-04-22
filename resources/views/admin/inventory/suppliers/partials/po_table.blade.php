<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover">
        <thead class="table-light">
            <tr>
                <th>PO Number</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Score</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $po)
                <tr>
                    <td><code>{{ $po->po_number }}</code></td>
                    <td>{{ $po->order_date->format('M d, Y') }}</td>
                    <td>
                        @php
                            $statusClass = match($po->status) {
                                'Draft' => 'badge-soft-secondary',
                                'Sent' => 'badge-soft-info',
                                'Delivered' => 'badge-soft-success',
                                default => 'badge-soft-dark'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $po->status }}</span>
                    </td>
                    <td>
                        @if($po->status === 'Delivered' && $po->performance_score !== null)
                            @php
                                $score = $po->performance_score;
                                $scoreClass = 'text-danger';
                                if ($score >= 80) $scoreClass = 'text-success';
                                elseif ($score >= 50) $scoreClass = 'text-warning';
                            @endphp
                            <span class="fw-bold {{ $scoreClass }}">
                                <iconify-icon icon="solar:star-bold" class="align-middle"></iconify-icon>
                                {{ $score }}%
                            </span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="fw-bold">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($po->total_amount, 2) }}</td>
                    <td>
                        <a href="{{ route('admin.inventory.po.show', $po->id) }}" class="btn btn-soft-primary btn-sm">
                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-16"></iconify-icon>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No purchase orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted small">
            Showing <span class="fw-semibold">{{ $purchaseOrders->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $purchaseOrders->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $purchaseOrders->total() }}</span> Results
        </div>
        <div>
            {{ $purchaseOrders->links() }}
        </div>
    </div>
</div>
