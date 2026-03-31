@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Adjustment Details: {{ $adjustment->adjustment_number }}</h4>
            <a href="{{ route('admin.inventory.adjustment.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Adjustment Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#SL</th>
                                        <th>Product / Variant</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($adjustment->items as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-medium">{{ $item->product->name }}</div>
                                                @if($item->variant)
                                                    <small class="text-muted">Variant: {{ $item->variant->variant_name }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ number_format($item->unit_cost, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->quantity * $item->unit_cost, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Grand Totals:</th>
                                        <th class="text-center">{{ $adjustment->items->sum('quantity') }}</th>
                                        <th></th>
                                        <th class="text-end">
                                            @php 
                                                $total = $adjustment->items->sum(fn($i) => $item->quantity * $item->unit_cost);
                                            @endphp
                                            {{ number_format($total, 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                @if($adjustment->remarks)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Remarks</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $adjustment->remarks }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted mb-1">Adjustment Number:</label>
                            <div class="fw-bold">{{ $adjustment->adjustment_number }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Warehouse:</label>
                            <div class="fw-bold">{{ $adjustment->warehouse->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Batch Number:</label>
                            <div class="fw-bold text-primary">{{ $adjustment->batch->batch_number }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Adjustment Date:</label>
                            <div class="fw-bold">{{ $adjustment->adjustment_date->format('d M, Y') }}</div>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted mb-1">Created By:</label>
                            <div class="fw-bold">{{ $adjustment->creator->name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
