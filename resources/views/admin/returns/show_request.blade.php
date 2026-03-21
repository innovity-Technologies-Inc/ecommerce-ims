@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Return Request #{{ $request->return_id }}</h4>
        <a href="{{ route('admin.returns.requests') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i>Back to List
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Return Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th class="text-center">Return Qty</th>
                                    <th class="text-center">Condition</th>
                                    <th class="text-end pe-3">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($request->returnItems as $item)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $item->product->primary_image ? asset($item->product->primary_image) : asset('admin_assets/images/no-image.png') }}" class="rounded-pill" style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fs-14">{{ $item->product->name }}</h6>
                                                    @if($item->productVariant)
                                                        <small class="text-muted">{{ $item->productVariant->variant_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-center">
                                            @if($request->status === 'pending')
                                                <span class="badge bg-secondary-subtle text-secondary">Pending</span>
                                            @else
                                                <span class="badge {{ $item->condition === 'intact' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                    {{ ucfirst($item->condition) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Return Reason & Image</h5>
                </div>
                <div class="card-body">
                    <p class="mb-4"><strong>Reason:</strong><br>{{ $request->reason }}</p>
                    @if($request->image)
                        <h6 class="mb-3">Uploaded Image:</h6>
                        <img src="{{ asset($request->image) }}" class="img-fluid rounded border shadow-sm" style="max-width: 300px;">
                    @else
                        <div class="alert alert-secondary py-2">No image uploaded.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer & Order Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><strong>Order ID:</strong> #{{ $request->order->order_id }}</li>
                        <li class="mb-2"><strong>Customer:</strong> {{ $request->user ? $request->user->name : 'Guest' }}</li>
                        <li class="mb-2"><strong>Email:</strong> {{ $request->user ? $request->user->email : $request->order->email }}</li>
                        <li class="mb-2"><strong>Mobile:</strong> {{ $request->user ? $request->user->mobile : $request->order->mobile }}</li>
                        <li class="mb-0"><strong>Status:</strong> 
                            <span class="badge {{ match($request->status) {
                                'pending' => 'bg-warning-subtle text-warning',
                                'approved' => 'bg-info-subtle text-info',
                                'received' => 'bg-success-subtle text-success',
                                'rejected' => 'bg-danger-subtle text-danger',
                                default => 'bg-secondary-subtle text-secondary'
                            } }} px-2 py-1">
                                {{ ucfirst($request->status) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            @if($request->status === 'pending')
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Action</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.returns.update_status', $request->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Update Status</label>
                                <select name="status" id="status_toggle" class="form-select" required>
                                    <option value="">Select Action</option>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                </select>
                            </div>

                            <div id="rejection_container" class="d-none mb-3">
                                <label class="form-label">Rejection Reason</label>
                                <textarea name="rejection_reason" class="form-control" rows="3"></textarea>
                            </div>

                            <div id="condition_container" class="d-none mb-3">
                                <label class="form-label fw-bold mb-3 border-bottom pb-2 w-100">Set Item Conditions</label>
                                @foreach($request->returnItems as $item)
                                    <div class="mb-3">
                                        <p class="mb-1 small fw-bold">{{ $item->product->name }}</p>
                                        <select name="items[{{ $item->id }}][condition]" class="form-select form-select-sm" required>
                                            <option value="intact">Intact (Restock)</option>
                                            <option value="damage">Damage (Wastage)</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Confirm Action</button>
                        </form>
                    </div>
                </div>
            @elseif($request->status === 'approved')
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">Receiving Workflow</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-4">The return request is approved. Once you have physically received the products, click the button below to process stock and sales adjustments.</p>
                        <form action="{{ route('admin.returns.receive', $request->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                                <i class="bx bx-check-circle me-1"></i> MARK AS RECEIVED
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($request->status === 'rejected')
                <div class="card border-danger mb-3">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0 text-white">Rejection Details</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><strong>Reason:</strong><br>{{ $request->rejection_reason }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#status_toggle').on('change', function() {
            const val = $(this).val();
            if (val === 'approved') {
                $('#condition_container').removeClass('d-none');
                $('#rejection_container').addClass('d-none');
                $('#rejection_container textarea').removeAttr('required');
            } else if (val === 'rejected') {
                $('#rejection_container').removeClass('d-none');
                $('#rejection_container textarea').attr('required', 'required');
                $('#condition_container').addClass('d-none');
            } else {
                $('#condition_container').addClass('d-none');
                $('#rejection_container').addClass('d-none');
            }
        });
    });
</script>
@endsection
