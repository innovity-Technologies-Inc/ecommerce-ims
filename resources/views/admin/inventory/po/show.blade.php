@extends('admin.structure.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="container-xxl">
    <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Purchase Order: {{ $po->po_number }}</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.inventory.po.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Back
                        </a>
                        @if($po->status !== 'Delivered')
                            @can('po.edit')
                            <a href="{{ route('admin.inventory.po.edit', $po->id) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-edit me-1"></i> Edit PO
                            </a>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product / Variant</th>
                                        <th class="text-center">Order Qty</th>
                                        <th class="text-center">Received Qty</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($po->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->product->name }}
                                                @if($item->variant)
                                                    <br><small class="text-muted">Variant: {{ $item->variant->variant_name }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->order_quantity }}</td>
                                            <td class="text-center">{{ $item->received_quantity }}</td>
                                            <td class="text-end">{{ number_format($item->unit_cost, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Grand Total:</th>
                                        <th class="text-end">{{ number_format($po->total_amount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($po->notes)
                            <div class="mt-4">
                                <h6>Notes:</h6>
                                <p class="text-muted">{{ $po->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Summary</h5>
                        
                        <div class="mb-3">
                            <label class="text-muted mb-1">Status:</label>
                            <div>
                                @php
                                    $badgeClass = match($po->status) {
                                        'Draft' => 'bg-secondary',
                                        'Sent' => 'bg-info',
                                        'Delivered' => 'bg-success',
                                        default => 'bg-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} fs-14">{{ $po->status }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted mb-1">Supplier:</label>
                            <div class="fw-bold">{{ $po->supplier->name }}</div>
                            <div class="text-muted small">{{ $po->supplier->email }}</div>
                            <div class="text-muted small">{{ $po->supplier->mobile }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted mb-1">Order Date:</label>
                            <div class="fw-bold">{{ $po->order_date->format('M d, Y') }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted mb-1">Expected Delivery:</label>
                            <div class="fw-bold text-info">{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'N/A' }}</div>
                        </div>

                        @if($po->received_date)
                            <div class="mb-3">
                                <label class="text-muted mb-1">Received Date:</label>
                                <div class="fw-bold text-success">{{ $po->received_date->format('M d, Y') }}</div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="text-muted mb-1">Created By:</label>
                            <div class="fw-bold">{{ $po->creator->name ?? 'System' }}</div>
                        </div>

                        @if($po->status !== 'Delivered')
                        <hr>
                        <form action="{{ route('admin.inventory.po.update-status', $po->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="statusUpdate" class="form-label">Update Status</label>
                                <select name="status" id="statusUpdate" class="form-select">
                                    <option value="Draft" {{ $po->status == 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Sent" {{ $po->status == 'Sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="Delivered" {{ $po->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                            </div>

                            <div id="receivedDateContainer" class="mb-3" style="display: none;">
                                <label for="received_date" class="form-label">Received Date</label>
                                <input type="date" name="received_date" id="received_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>

                            <div id="notifySupplierContainer" class="mb-3" style="display: none;">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="notify_supplier" id="notifyUpdate" value="1" {{ $po->notify_supplier ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notifyUpdate">Notify Supplier by Email</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-soft-success w-100 mt-2">Update Status</button>
                            </form>
                            @endif
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
                            @endsection

                            @section('scripts')
                            <script>
                            $(document).ready(function() {
                            $('#statusUpdate').change(function() {
                            const status = $(this).val();

                            if (status === 'Delivered') {
                            $('#receivedDateContainer').show();
                            } else {
                            $('#receivedDateContainer').hide();
                            }

                            if (status === 'Sent') {
                            $('#notifySupplierContainer').show();
                            } else {
                            $('#notifySupplierContainer').hide();
                            }
                            });

                            // Trigger on load
                            $('#statusUpdate').trigger('change');
                            });
                            </script>
                            @endsection
