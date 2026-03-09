<x-mail::message>
# Order Confirmation

Thank you for your order, {{ $order->name }}!

Your order has been placed successfully and is currently being processed.

**Order ID:** {{ $order->order_id }}
**Order Date:** {{ $order->created_at->format('M d, Y') }}

<x-mail::table>
| Product       | Qty | Price | Total |
| :------------ | :-: | :---: | ----: |
@foreach($order->orderItems as $item)
| {{ $item->product_name }} {{ $item->variant_name ? '(' . $item->variant_name . ')' : '' }} | {{ $item->quantity }} | ${{ number_format($item->unit_price, 2) }} | ${{ number_format($item->total_price, 2) }} |
@endforeach
| **Total**     |     |       | **${{ number_format($order->total_amount, 2) }}** |
</x-mail::table>

**Shipping Address:**
{{ $order->address }}
{{ $order->city }}, {{ $order->state }} {{ $order->zip }}
{{ $order->country }}

**Payment Method:** {{ $order->payment_method }}
**Order Status:** {{ $order->order_status }}

If you have any questions, please reply to this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
