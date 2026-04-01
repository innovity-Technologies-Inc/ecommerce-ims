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
                                <label for="batch_number" class="form-label">Batch Number <span class="text-danger">*</span></label>
                                <input type="text" name="batch_number" id="batch_number" class="form-control @error('batch_number') is-invalid @enderror" 
                                       placeholder="Enter global batch number"
                                       value="{{ old('batch_number', $po->po_number . '-R' . date('ymd')) }}" required>
                                @error('batch_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="received_date" class="form-label">Received Date <span class="text-danger">*</span></label>
                                <input type="date" name="received_date" id="received_date" class="form-control @error('received_date') is-invalid @enderror" 
                                       value="{{ old('received_date', date('Y-m-d')) }}" required>
                                @error('received_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
                            <i class="bx bx-info-circle me-1"></i> Enter quantities for each item. For serial numbers, you can add multiple tags.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product / Variant</th>
                                        <th class="text-center" style="width: 80px;">Ordered</th>
                                        <th style="width: 110px;">Received Qty</th>
                                        <th>Received Serials</th>
                                        <th style="width: 110px;">Damaged Qty</th>
                                        <th>Damaged Serials</th>
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
                                                <span class="badge badge-soft-info fs-13 ordered-qty" data-ordered="{{ $item->order_quantity }}">{{ $item->order_quantity }}</span>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $item->id }}][received_quantity]" 
                                                       class="form-control received-qty @error('items.'.$item->id.'.received_quantity') is-invalid @enderror" 
                                                       min="0" 
                                                       max="{{ $item->order_quantity }}"
                                                       value="{{ old('items.'.$item->id.'.received_quantity', $item->order_quantity) }}" required>
                                                @error('items.'.$item->id.'.received_quantity')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <div class="serial-container">
                                                    <select name="items[{{ $item->id }}][received_serials][]" 
                                                            class="form-control serial-tags received-serials @error('items.'.$item->id.'.received_serials') is-invalid @enderror" 
                                                            multiple="multiple">
                                                    </select>
                                                </div>
                                                @error('items.'.$item->id.'.received_serials')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="small text-muted mt-1 serial-count"></div>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $item->id }}][damaged_quantity]" 
                                                       class="form-control text-danger damaged-qty @error('items.'.$item->id.'.damaged_quantity') is-invalid @enderror" 
                                                       min="0" 
                                                       max="{{ $item->order_quantity }}"
                                                       value="{{ old('items.'.$item->id.'.damaged_quantity', 0) }}">
                                                @error('items.'.$item->id.'.damaged_quantity')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <div class="serial-container">
                                                    <select name="items[{{ $item->id }}][damaged_serials][]" 
                                                            class="form-control serial-tags damaged-serials @error('items.'.$item->id.'.damaged_serials') is-invalid @enderror" 
                                                            multiple="multiple">
                                                    </select>
                                                </div>
                                                @error('items.'.$item->id.'.damaged_serials')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="small text-muted mt-1 serial-count"></div>
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
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 38px;
        max-height: 100px; /* Limit height and scroll vertically */
        overflow-y: auto; 
        overflow-x: hidden;
        border: 1px solid #dee2e6;
        border-radius: 0;
        display: flex;
        align-items: center;
        width: 100%;
        scrollbar-width: thin;
    }
    /* Webkit scrollbar styling */
    .select2-container--bootstrap-5 .select2-selection--multiple::-webkit-scrollbar {
        width: 4px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 10px;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection--multiple {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        padding: 0 0.5rem;
        display: flex;
        flex-wrap: wrap; /* Re-enable wrapping for vertical growth */
        align-items: center;
        width: 100%;
        margin: 0;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-search {
        flex-grow: 1;
        display: flex;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-search__field {
        margin: 0;
        padding-left: 0.25rem;
        height: 34px;
        font-family: inherit;
        line-height: 34px;
        width: 100% !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        margin-top: 2px;
        margin-bottom: 2px;
        white-space: nowrap;
    }
    .serial-container {
        min-width: 200px;
    }
    .received-qty, .damaged-qty {
        border-radius: 0;
    }
</style>
<script>
    $(document).ready(function() {
        $('.serial-tags').select2({
            theme: 'bootstrap-5',
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: 'Add serial numbers'
        });

        // Prevent exceeding quantity during selection
        $('.serial-tags').on('select2:selecting', function(e) {
            let tr = $(this).closest('tr');
            let isReceived = $(this).hasClass('received-serials');
            let qty = parseInt(tr.find(isReceived ? '.received-qty' : '.damaged-qty').val()) || 0;
            let currentCount = $(this).val() ? $(this).val().length : 0;

            if (currentCount >= qty) {
                toastr.warning(`Cannot exceed the ${isReceived ? 'Received' : 'Damaged'} quantity (${qty}).`);
                e.preventDefault();
            }
        });

        $('.serial-tags').on('change', function() {
            let count = $(this).val() ? $(this).val().length : 0;
            $(this).closest('td').find('.serial-count').text(count > 0 ? `Selected: ${count} serials` : '');
        });

        // Auto-calculation between received and damaged quantities
        $('.received-qty').on('input', function() {
            let tr = $(this).closest('tr');
            let ordered = parseInt(tr.find('.ordered-qty').data('ordered')) || 0;
            let received = parseInt($(this).val()) || 0;
            
            if (received > ordered) {
                received = ordered;
                $(this).val(received);
            }
            
            let damaged = ordered - received;
            tr.find('.damaged-qty').val(damaged);
        });

        $('.damaged-qty').on('input', function() {
            let tr = $(this).closest('tr');
            let ordered = parseInt(tr.find('.ordered-qty').data('ordered')) || 0;
            let damaged = parseInt($(this).val()) || 0;
            
            if (damaged > ordered) {
                damaged = ordered;
                $(this).val(damaged);
            }
            
            let received = ordered - damaged;
            tr.find('.received-qty').val(received);
        });

        $('#receiveForm').on('submit', function(e) {
            let isValid = true;
            $('tbody tr').each(function() {
                let productName = $(this).find('strong').text();
                let receivedQty = parseInt($(this).find('.received-qty').val()) || 0;
                let damagedQty = parseInt($(this).find('.damaged-qty').val()) || 0;
                
                let receivedSerials = $(this).find('.received-serials').val() || [];
                let damagedSerials = $(this).find('.damaged-serials').val() || [];
                
                if (receivedSerials.length > 0 && receivedSerials.length !== receivedQty) {
                    toastr.error(`Received serial count (${receivedSerials.length}) for ${productName} must match Received Qty (${receivedQty}).`);
                    isValid = false;
                }
                
                if (damagedSerials.length > 0 && damagedSerials.length !== damagedQty) {
                    toastr.error(`Damaged serial count (${damagedSerials.length}) for ${productName} must match Damaged Qty (${damagedQty}).`);
                    isValid = false;
                }
            });
            
            if (!isValid) e.preventDefault();
        });
    });
</script>
@endsection
