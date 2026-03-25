@extends('admin.structure.app')

@section('title', 'Receive Purchase Order')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Receive Purchase Order: {{ $po->po_number }}</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.inventory.po.show', $po->id) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.inventory.po.process-receive', $po->id) }}" method="POST" id="receiveForm">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Receiving Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="batch_number" class="form-label">Batch Number</label>
                                <input type="text" name="batch_number" id="batch_number" class="form-control" placeholder="Enter batch number (e.g. BATCH-2024-001)">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="received_date" class="form-label">Received Date <span class="text-danger">*</span></label>
                                <input type="date" name="received_date" id="received_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Supplier</label>
                                <input type="text" class="form-control" value="{{ $po->supplier->name }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Receiving</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-1"></i> For serial numbers, use ranges like <strong>SN001 - SN300</strong> or comma-separated values like <strong>SN302, SN305</strong>.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product / Variant</th>
                                        <th class="text-center" style="width: 100px;">Ordered</th>
                                        <th class="text-center" style="width: 120px;">Received</th>
                                        <th class="text-center" style="width: 120px;">Damaged</th>
                                        <th class="text-center" style="width: 120px;">Missing</th>
                                        <th>Serial Numbers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($po->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->product->name }}</strong>
                                                @if($item->variant)
                                                    <br><small class="text-muted">Variant: {{ $item->variant->variant_name }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-soft-info fs-13">{{ $item->order_quantity }}</span>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $item->id }}][received_quantity]" 
                                                       class="form-control received-qty" 
                                                       min="0" max="{{ $item->order_quantity }}" 
                                                       value="{{ $item->order_quantity }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $item->id }}][damaged_quantity]" 
                                                       class="form-control text-danger" 
                                                       min="0" value="0">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $item->id }}][missing_quantity]" 
                                                       class="form-control text-warning" 
                                                       min="0" value="0">
                                            </td>
                                            <td>
                                                <textarea name="items[{{ $item->id }}][serial_numbers]" 
                                                          class="form-control serial-input" 
                                                          rows="2" 
                                                          placeholder="SN001 - SN100"></textarea>
                                                <div class="small text-muted mt-1 parsed-count"></div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-success px-5">Confirm Receipt & Update Inventory</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Simple client-side feedback for serial number count (optional but helpful)
        $('.serial-input').on('input', function() {
            let input = $(this).val().trim();
            let count = 0;
            if (input) {
                let parts = input.split(',');
                parts.forEach(part => {
                    part = part.trim();
                    if (part.includes('-')) {
                        let range = part.split('-');
                        let start = range[0].trim().match(/\d+$/);
                        let end = range[1].trim().match(/\d+$/);
                        if (start && end) {
                            count += (parseInt(end[0]) - parseInt(start[0]) + 1);
                        }
                    } else if (part) {
                        count++;
                    }
                });
            }
            $(this).siblings('.parsed-count').text(count > 0 ? `Detected: ${count} serial numbers` : '');
        });
    });
</script>
@endsection
