<div class="table-content table-responsive cart-table-content mt-4">
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
                    <td class="product-name text-start ps-3">
                        <span class="fw-bold text-dark">{{ $item->product_name }}</span>
                        @if($item->variant_name)
                            <br><small class="text-muted">{{ $item->variant_name }}</small>
                        @endif
                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                        <input type="hidden" name="items[{{ $index }}][product_variant_id]" value="{{ $item->product_variant_id }}">
                        <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}">
                    </td>
                    <td class="product-quantity text-center">
                        <span class="badge bg-light text-dark border">{{ $item->quantity }}</span>
                    </td>
                    <td class="product-quantity text-center">
                        <div class="cart-plus-minus d-inline-block">
                            <input class="cart-plus-minus-box item-qty" type="number" name="items[{{ $index }}][quantity]" value="0" min="0" max="{{ $item->quantity }}" style="width: 60px; height: 35px; text-align: center; border: 1px solid #ebebeb;">
                        </div>
                    </td>
                    <td class="product-subtotal text-end pe-3">
                        <span class="fw-bold text-dark">${{ number_format($item->unit_price, 2) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
