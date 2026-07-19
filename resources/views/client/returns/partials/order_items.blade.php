<div class="table-content table-responsive cart-table-content mt-4">
    @php $gs = \App\HelperClass::generalSettings(); @endphp
    <table class="w-100">
        <thead>
            <tr>
                <th class="text-start ps-3">Product</th>
                <th class="text-center">Ordered Qty</th>
                <th class="text-center">Return Qty</th>
                <th class="text-end pe-3">Unit Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $index => $item)
                <tr>
                    <td class="product-thumbnail text-start ps-3">
                        <div class="d-flex align-items-center gap-3 py-2">
                            <img src="{{ $item->product->primaryImage ? \App\HelperClass::file_url($item->product->primaryImage->image_path) : asset('admin_assets/images/no-image.png') }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                    </td>
                    <td class="product-name">
                        <div class="py-2">
                            <span class="fw-bold text-dark d-block">{{ $item->product_name }}</span>
                            @if($item->variant_name)
                                <small class="text-muted d-block">{{ $item->variant_name }}</small>
                            @endif
                        </div>
                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                        <input type="hidden" name="items[{{ $index }}][product_variant_id]" value="{{ $item->product_variant_id }}">
                        <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}">
                    </td>
                    <td class="product-quantity text-center">
                        <div class="cart-plus-minus d-inline-block">
                            <input class="cart-plus-minus-box item-qty" type="number" name="items[{{ $index }}][quantity]" value="0" min="0" max="{{ $item->quantity }}" style="width: 60px; height: 35px; text-align: center; border: 1px solid #ebebeb;">
                        </div>
                    </td>
                    <td class="product-subtotal text-end pe-3">
                        <span class="fw-bold text-dark">{{ $gs->currency ?? '$' }}{{ number_format($item->unit_price, 2) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
