@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">{{ isset($coupon) ? 'Edit Coupon' : 'Create Coupon' }}</h4>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <form action="{{ isset($coupon) ? route('admin.coupons.update', $coupon->id) : route('admin.coupons.store') }}" method="POST">
            @csrf
            @if(isset($coupon))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-light-subtle">
                            <h5 class="card-title mb-0">General Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                        value="{{ old('code', $coupon->code ?? '') }}" placeholder="e.g. SAVE20" required>
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Apply For <span class="text-danger">*</span></label>
                                    <select name="apply_for" class="form-select @error('apply_for') is-invalid @enderror" required>
                                        <option value="total_product_price" {{ old('apply_for', $coupon->apply_for ?? '') == 'total_product_price' ? 'selected' : '' }}>Total Product Price</option>
                                        <option value="shipping_cost" {{ old('apply_for', $coupon->apply_for ?? '') == 'shipping_cost' ? 'selected' : '' }}>Shipping Cost</option>
                                    </select>
                                    @error('apply_for') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                    <select name="discount_type" id="discount_type" class="form-select @error('discount_type') is-invalid @enderror" required>
                                        <option value="percentage" {{ old('discount_type', $coupon->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                        <option value="fixed" {{ old('discount_type', $coupon->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    </select>
                                    @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Discount Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="amount-addon">{{ old('discount_type', $coupon->discount_type ?? 'percentage') == 'percentage' ? '%' : '$' }}</span>
                                        <input type="number" name="discount_amount" step="0.01" class="form-control @error('discount_amount') is-invalid @enderror" 
                                            value="{{ old('discount_amount', $coupon->discount_amount ?? '') }}" required>
                                    </div>
                                    @error('discount_amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-lg-6" id="max_discount_wrapper" style="{{ old('discount_type', $coupon->discount_type ?? 'percentage') == 'fixed' ? 'display: none;' : '' }}">
                                    <label class="form-label">Maximum Discount Amount</label>
                                    <input type="number" name="max_discount_amount" step="0.01" class="form-control @error('max_discount_amount') is-invalid @enderror" 
                                        value="{{ old('max_discount_amount', $coupon->max_discount_amount ?? '') }}">
                                    <small class="text-muted">Maximum limit for percentage discount.</small>
                                    @error('max_discount_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Minimum Spend</label>
                                    <input type="number" name="min_spend" step="0.01" class="form-control @error('min_spend') is-invalid @enderror" 
                                        value="{{ old('min_spend', $coupon->min_spend ?? '0') }}">
                                    @error('min_spend') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-light-subtle">
                            <h5 class="card-title mb-0">Limits & Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Usage Limit</label>
                                <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" 
                                    value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" placeholder="Leave empty for unlimited">
                                @error('usage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Active From <span class="text-danger">*</span></label>
                                <input type="date" name="active_on" class="form-control @error('active_on') is-invalid @enderror" 
                                    value="{{ old('active_on', isset($coupon) ? $coupon->active_on->toDateString() : date('Y-m-d')) }}" required>
                                @error('active_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Expired On <span class="text-danger">*</span></label>
                                <input type="date" name="expired_on" class="form-control @error('expired_on') is-invalid @enderror" 
                                    value="{{ old('expired_on', isset($coupon) ? $coupon->expired_on->toDateString() : '') }}" required>
                                @error('expired_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check form-switch form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" 
                                        {{ old('status', $coupon->status ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusSwitch">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100">{{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}</button>
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
        $('#discount_type').on('change', function() {
            if ($(this).val() === 'percentage') {
                $('#max_discount_wrapper').slideDown();
                $('#amount-addon').text('%');
            } else {
                $('#max_discount_wrapper').slideUp();
                $('#amount-addon').text('$');
            }
        });
    });
</script>
@endsection
